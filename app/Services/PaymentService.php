<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use App\Models\Plan;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Midtrans\Snap;
use Midtrans\Config;

class PaymentService
{
    /**
     * Konfigurasi Midtrans saat service diinisialisasi
     */
    public function __construct()
    {
        Config::$serverKey = config('midtrans.server_key');
        Config::$isProduction = config('midtrans.is_production');
        Config::$isSanitized = config('midtrans.is_sanitized');
        Config::$is3ds = config('midtrans.is_3ds');
    }

    /**
     * Membuat transaksi baru dan mendapatkan snap token
     */
    public function createTransaction(User $user, Plan $plan, float $amount): array
    {
        $token = $this->generateAndCachePaymentToken($user);
        $transaction = $this->createTransactionRecord($user, $plan, $amount);
        $snapToken = $this->generateSnapToken($transaction);

        $transaction->update(['snap_token' => $snapToken]);

        return [
            'snap_token' => $snapToken,
            'validation_token' => $token
        ];
    }

    /**
     * Memproses callback dari Midtrans
     */
    public function handleCallback(array $callbackData): bool
    {
        if (!$this->validateSignature($callbackData)) {
            return false;
        }

        $transaction = Transaction::with(['user', 'plan'])
            ->where('transaction_number', $callbackData['order_id'])
            ->first();

        if (!$transaction) {
            return false;
        }

        $this->processTransactionStatus($transaction, $callbackData);
        return true;
    }

    /**
     * Memvalidasi dan memproses successful payment
     */
    public function processSuccessfulPayment(User $user, string $orderId, string $validationToken): bool
    {
        if (!$this->validatePaymentToken($user, $validationToken)) {
            return false;
        }

        $transaction = Transaction::with('plan')
            ->where('transaction_number', $orderId)
            ->firstOrFail();

        Cache::forget('payment_token_' . $user->id);

        return true;
    }

    private function generateAndCachePaymentToken(User $user): string
    {
        $token = Str::random(32);
        Cache::put('payment_token_' . $user->id, $token, now()->addMinutes(30));
        return $token;
    }

    private function createTransactionRecord(User $user, Plan $plan, float $amount): Transaction
    {
        return Transaction::create([
            'user_id' => $user->id,
            'plan_id' => $plan->id,
            'transaction_number' => 'ORDER-' . time() . '-' . $user->id,
            'total_amount' => $amount,
            'payment_status' => 'pending'
        ]);
    }

    private function generateSnapToken(Transaction $transaction): string
    {
        $payload = [
            'transaction_details' => [
                'order_id' => $transaction->transaction_number,
                'gross_amount' => (int) $transaction->total_amount,
            ],
            'customer_details' => [
                'first_name' => $transaction->user->name,
                'email' => $transaction->user->email,
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

        return Snap::getSnapToken($payload);
    }

    private function validateSignature(array $callbackData): bool
    {
        $serverKey = config('midtrans.server_key');
        $hashed = hash(
            'sha512',
            $callbackData['order_id'] .
                $callbackData['status_code'] .
                $callbackData['gross_amount'] .
                $serverKey
        );

        return $hashed === $callbackData['signature_key'];
    }

    private function processTransactionStatus(Transaction $transaction, array $callbackData): void
    {
        $status = $this->determinePaymentStatus($callbackData['transaction_status']);

        if ($status === 'success') {
            $this->handleSuccessfulPayment($transaction, $callbackData);
        } else {
            $this->updateTransactionStatus($transaction, $status, $callbackData['transaction_id']);
        }
    }

    private function determinePaymentStatus(string $midtransStatus): string
    {
        if (in_array($midtransStatus, ['capture', 'settlement'])) {
            return 'success';
        }
        if (in_array($midtransStatus, ['deny', 'cancel', 'expire'])) {
            return 'failed';
        }
        return 'pending';
    }

    private function handleSuccessfulPayment(Transaction $transaction, array $callbackData): void
    {
        try {
            DB::transaction(function () use ($transaction, $callbackData) {
                $this->createMembership($transaction);
                $this->updateTransactionStatus($transaction, 'success', $callbackData['transaction_id']);
            });
        } catch (\Exception $e) {
            Log::error('Failed to process successful payment: ' . $e->getMessage());
            throw $e;
        }
    }

    private function createMembership(Transaction $transaction): void
    {
        $transaction->user->memberships()->create([
            'plan_id' => $transaction->plan_id,
            'start_date' => now(),
            'end_date' => now()->addDays($transaction->plan->duration),
            'active' => true,
        ]);
    }

    private function updateTransactionStatus(Transaction $transaction, string $status, string $midtransId): void
    {
        $transaction->update([
            'payment_status' => $status,
            'midtrans_transaction_id' => $midtransId,
        ]);
    }

    private function validatePaymentToken(User $user, string $token): bool
    {
        $validToken = Cache::get('payment_token_' . $user->id);
        return $validToken && $validToken === $token;
    }
}
