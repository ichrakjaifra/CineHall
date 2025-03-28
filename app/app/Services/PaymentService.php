<?php

namespace App\Services;

use Stripe\Stripe;
use Stripe\PaymentIntent;
use App\Models\Reservation;
use App\Repositories\Contracts\ReservationRepositoryInterface;

class PaymentService
{
    private $reservationRepository;

    public function __construct(ReservationRepositoryInterface $reservationRepository)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
        $this->reservationRepository = $reservationRepository;
    }

    public function createPaymentIntent(Reservation $reservation)
    {
        $amount = $reservation->tickets->sum('price') * 100; // Stripe amount is in cents
        
        return PaymentIntent::create([
            'amount' => $amount,
            'currency' => 'eur',
            'metadata' => [
                'reservation_id' => $reservation->id,
            ],
        ]);
    }

    public function handlePaymentSuccess(string $paymentIntentId)
    {
        $paymentIntent = PaymentIntent::retrieve($paymentIntentId);
        
        $reservationId = $paymentIntent->metadata->reservation_id;
        
        return $this->reservationRepository->confirmPayment(
            $reservationId,
            'stripe',
            $paymentIntentId
        );
    }
}