<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Services\DeviceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;

class TransactionController extends Controller
{
    protected $deviceService;

    public function __construct(DeviceService $deviceService)
    {
        // Konfigurasi Midtrans
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');

        $this->deviceService = $deviceService;
    }

    public function checkout(Request $request)
    {
        $user = Auth::user();

        // Generate token untuk validasi success callback
        $token = Str::random(32);

        // Simpan token di cache dengan waktu expired 30 menit
        Cache::put('payment_token_' . $user->id, $token, now()->addMinutes(30));

        // Validasi request
        $request->validate([
            'plan_id' => 'required|exists:plans,id',
        ]);

        // Generate order number
        $transactionNumber = 'ORDER-' . time() . '-' . $user->id;

        // Create order
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'plan_id' => $request->plan_id,
            'transaction_number' => $transactionNumber,
            'total_amount' => $request->amount,
            'payment_status' => 'pending'
        ]);

        // Set up Midtrans payment
        $payload = [
            'transaction_details' => [
                'order_id' => $transaction->transaction_number,
                'gross_amount' => (int) $transaction->total_amount,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
                'phone' => '000000000000',
            ],
            'item_details' => [
                [
                    'id' => $transaction->plan_id,
                    'price' => (int) $transaction->total_amount,
                    'quantity' => 1,
                    'name' => $transaction->plan->title,
                ]
            ],
        ];

        try {
            // Get Snap Payment Page URL
            $snapToken = Snap::getSnapToken($payload);
            $transaction->update(['snap_token' => $snapToken]);

            return response()->json([
                'status' => 'success',
                'snap_token' => $snapToken,
                'validation_token' => $token
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function callback(Request $request)
    {
        $serverKey = config('midtrans.server_key');
        $hashed = hash('sha512', $request->order_id . $request->status_code . $request->gross_amount . $serverKey);

        if ($hashed == $request->signature_key) {
            $transaction = Transaction::with(['user', 'plan'])->where('transaction_number', $request->order_id)->first();

            if ($transaction) {
                $paymentStatus = 'pending';

                if ($request->transaction_status == 'capture' || $request->transaction_status == 'settlement') {
                    $paymentStatus = 'success';

                    // Create membership after successful payment
                    $user = $transaction->user;
                    $plan = $transaction->plan;

                    try {
                        DB::transaction(function () use ($user, $plan, $request, $transaction) {
                            // Create membership
                            $user->memberships()->create([
                                'plan_id' => $plan->id,
                                'start_date' => now(),
                                'end_date' => now()->addDays($plan->duration),
                                'active' => true,
                            ]);

                            // Update order status
                            $transaction->update([
                                'payment_status' => 'success',
                                'midtrans_transaction_id' => $request->transaction_id,
                            ]);
                        });
                    } catch (\Exception $e) {
                        Log::error('Failed to process successful payment: ' . $e->getMessage());
                        return response()->json(['status' => 'error', 'message' => 'Failed to process membership'], 500);
                    }
                } elseif ($request->transaction_status == 'deny' || $request->transaction_status == 'cancel' || $request->transaction_status == 'expire') {
                    $paymentStatus = 'failed';
                    $transaction->update([
                        'payment_status' => $paymentStatus,
                        'midtrans_transaction_id' => $request->transaction_id,
                    ]);
                }

                return response()->json(['status' => 'success']);
            }
        }

        return response()->json(['status' => 'error'], 404);
    }

    public function handleSuccess(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string',
            'validation_token' => 'required|string',
        ]);

        $user = Auth::user();

        // Validasi token
        $validToken = Cache::get('payment_token_' . $user->id);

        if (!$validToken || $validToken !== $request->validation_token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid request'
            ], 403);
        }

        try {
            $transaction = Transaction::with('plan')->where('transaction_number', $request->order_id)->firstOrFail();
            $maxDevices = $transaction->plan->max_devices;

            // Set payment registration mode
            $this->deviceService->setPaymentRegistration(true, $maxDevices);

            // Register device
            $this->deviceService->registerDevice($user);

            // Hapus token setelah berhasil
            Cache::forget('payment_token_' . $user->id);

            // Reset payment registration mode
            $this->deviceService->setPaymentRegistration(false);

            // Hapus token
            Cache::forget('payment_token_' . $user->id);

            return response()->json([
                'status' => 'success',
                'message' => 'Device registered successfully',
                'redirect_url' => route('subscription.success')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}