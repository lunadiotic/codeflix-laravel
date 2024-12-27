<?php

namespace App\Http\Controllers;

use App\Models\Plan;
use App\Models\Transaction;
use App\Services\DeviceService;
use App\Services\PaymentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    /**
     * @var PaymentService
     */
    protected $paymentService;

    /**
     * @var DeviceService
     */
    protected $deviceService;

    /**
     * Constructor dengan DI untuk services yang dibutuhkan
     */
    public function __construct(PaymentService $paymentService, DeviceService $deviceService)
    {
        $this->paymentService = $paymentService;
        $this->deviceService = $deviceService;
    }

    /**
     * Handle checkout request
     */
    public function checkout(Request $request)
    {
        $request->validate(['plan_id' => 'required|exists:plans,id']);

        try {
            $user = Auth::user();
            $plan = Plan::findOrFail($request->plan_id);

            $result = $this->paymentService->createTransaction(
                $user,
                $plan,
                $request->amount
            );

            return response()->json([
                'status' => 'success',
                'snap_token' => $result['snap_token'],
                'validation_token' => $result['validation_token']
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle Midtrans callback
     */
    public function callback(Request $request)
    {
        try {
            $success = $this->paymentService->handleCallback($request->all());

            return response()->json([
                'status' => $success ? 'success' : 'error'
            ], $success ? 200 : 404);
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle successful payment completion
     */
    public function handleSuccess(Request $request)
    {
        $request->validate([
            'order_id' => 'required|string',
            'validation_token' => 'required|string',
        ]);

        try {
            $user = Auth::user();

            if (!$this->paymentService->processSuccessfulPayment(
                $user,
                $request->order_id,
                $request->validation_token
            )) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid request'
                ], 403);
            }

            $transaction = Transaction::with('plan')
                ->where('transaction_number', $request->order_id)
                ->firstOrFail();

            // Register device after successful payment
            $this->deviceService->setPaymentRegistration(true, $transaction->plan->max_devices);
            $this->deviceService->registerDevice($user);
            $this->deviceService->setPaymentRegistration(false);

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
