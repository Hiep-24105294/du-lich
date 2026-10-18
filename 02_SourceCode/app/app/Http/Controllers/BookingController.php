<?php

namespace App\Http\Controllers;

use App\Exceptions\BookingException;
use App\Http\Requests\BookingStoreRequest;
use App\Http\Resources\BookingItemResource;
use App\Models\Booking;
use App\Models\TourSlot;
use App\Services\BookingService;
use App\Services\PaymentService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class BookingController extends Controller
{
    public function __construct(private readonly BookingService $bookings)
    {
    }

    public function store(BookingStoreRequest $request, TourSlot $slot): RedirectResponse
    {
        try {
            $booking = $this->bookings->hold($slot, $request->user(), $request->validated());
        } catch (BookingException $e) {
            throw ValidationException::withMessages([$e->field === 'slot' ? 'quantity' : $e->field => $e->getMessage()]);
        }

        return redirect(localized_route('bookings.show', ['booking' => $booking->code]))
            ->with('success', __('messages.booking.held', ['minutes' => $this->bookings->holdMinutes()]));
    }

    public function show(Request $request, Booking $booking): Response
    {
        $this->authorize('view', $booking);
        $booking->load(BookingItemResource::eager());

        return Inertia::render('Bookings/Show', [
            'booking' => BookingItemResource::one($booking),
            'sandboxGateway' => config('disanso.payment.gateway') === 'sandbox',
        ]);
    }

    public function pay(Request $request, Booking $booking, PaymentService $payments): RedirectResponse
    {
        $this->authorize('pay', $booking);
        try {
            $payment = $payments->initiate($booking, $request->user());
        } catch (BookingException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect(localized_route('payments.sandbox', ['payment' => $payment->txn_ref]));
    }

    public function cancel(Request $request, Booking $booking): RedirectResponse
    {
        $this->authorize('cancel', $booking);
        $wasPaid = $booking->status === Booking::PAID;
        try {
            $this->bookings->cancel($booking, $request->user(), 'user_cancelled');
        } catch (BookingException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', __($wasPaid ? 'messages.booking.refunded' : 'messages.booking.cancelled'));
    }
}
