<?php

namespace App\Services;

use App\Exceptions\BookingException;
use App\Models\Booking;
use App\Models\Payment;
use App\Models\User;
use App\Services\Payment\PaymentException;
use App\Services\Payment\PaymentGateway;
use Illuminate\Support\Facades\DB;

/**
 * Luồng thanh toán: khởi tạo giao dịch → cổng trả kết quả có chữ ký → xử lý callback (IPN).
 */
class PaymentService
{
    public function __construct(
        private readonly PaymentGateway $gateway,
        private readonly BookingService $bookings,
        private readonly AuditLogger $audit,
    ) {
    }

    public function gateway(): PaymentGateway
    {
        return $this->gateway;
    }

    public function initiate(Booking $booking, User $actor): Payment
    {
        if (! $this->bookings->canPay($booking)) {
            throw BookingException::cannotPay();
        }

        $payment = DB::transaction(function () use ($booking) {
            $existing = $booking->payments()->where('status', 'initiated')->latest('id')->first();
            if ($existing) {
                return $existing;
            }

            return $booking->payments()->create([
                'gateway' => $this->gateway->name(),
                'txn_ref' => $this->gateway->createTxnRef(),
                'amount_vnd' => $booking->total_vnd,
                'currency' => 'VND',
                'status' => 'initiated',
            ]);
        });

        if ($booking->status === Booking::HELD) {
            $this->bookings->transition($booking, Booking::PENDING_PAYMENT, $actor);
        }
        $this->audit->log('payment.initiated', $booking, ['txn_ref' => $payment->txn_ref, 'amount' => $payment->amount_vnd], $actor);

        return $payment;
    }

    /** Mô phỏng cổng: tạo payload + chữ ký cho kết quả người dùng chọn. */
    public function simulate(Payment $payment, string $outcome): array
    {
        $payload = $this->gateway->buildPayload($payment, $outcome);

        return ['payload' => $payload, 'signature' => $this->gateway->sign($payload)];
    }

    /**
     * Xử lý callback như IPN thật: xác minh chữ ký, đối chiếu số tiền, idempotent theo txn_ref.
     */
    public function handleCallback(array $payload, string $signature): Payment
    {
        if (! $this->gateway->verify($payload, $signature)) {
            $this->audit->log('payment.invalid_signature', null, ['txn_ref' => $payload['txn_ref'] ?? null]);
            throw PaymentException::invalidSignature();
        }

        $payment = Payment::query()->where('txn_ref', (string) ($payload['txn_ref'] ?? ''))->first();
        if (! $payment) {
            throw PaymentException::unknownTransaction();
        }
        if ((int) ($payload['amount'] ?? -1) !== (int) $payment->amount_vnd) {
            throw PaymentException::amountMismatch();
        }
        if ($payment->status !== 'initiated') {
            return $payment; // đã xử lý trước đó
        }

        $payment->fill(['gateway_payload' => $payload, 'signature' => $signature]);
        $outcome = (string) ($payload['outcome'] ?? 'fail');

        match ($outcome) {
            'success' => $this->onSuccess($payment),
            'cancel' => $this->onFailure($payment, 'cancelled'),
            default => $this->onFailure($payment, 'failed'),
        };

        $this->audit->log('payment.'.$payment->status, $payment->booking, ['txn_ref' => $payment->txn_ref, 'outcome' => $outcome]);

        return $payment->refresh();
    }

    private function onSuccess(Payment $payment): void
    {
        $booking = $payment->booking;
        if (! $booking->canTransitionTo(Booking::PAID) || ! $this->bookings->canPay($booking)) {
            // Giữ chỗ đã hết hạn/hủy: không nhận tiền, đánh dấu thất bại
            $payment->fill(['status' => 'failed'])->save();

            return;
        }
        $payment->fill(['status' => 'succeeded', 'paid_at' => now()])->save();
        $this->bookings->markPaid($booking, $booking->user);
    }

    /** Thất bại/hủy: booking quay lại "held" nếu còn hạn giữ chỗ, ngược lại hết hạn. */
    private function onFailure(Payment $payment, string $status): void
    {
        $payment->fill(['status' => $status])->save();
        $booking = $payment->booking;
        if ($booking->status !== Booking::PENDING_PAYMENT) {
            return;
        }
        $stillValid = $booking->hold_expires_at && $booking->hold_expires_at->isFuture();
        $this->bookings->transition($booking, $stillValid ? Booking::HELD : Booking::EXPIRED, $booking->user, $status);
    }
}
