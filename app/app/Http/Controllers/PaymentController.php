<?php

namespace App\Http\Controllers;

use App\Models\Reservation;
use App\Services\PaymentService;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    private $paymentService;

    public function __construct(PaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function createPaymentIntent(Request $request, $reservationId)
    {
        $reservation = Reservation::with('tickets')->findOrFail($reservationId);
        
        if ($reservation->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }
        
        if ($reservation->status !== 'pending') {
            return response()->json(['message' => 'Reservation is not pending payment'], 400);
        }
        
        $paymentIntent = $this->paymentService->createPaymentIntent($reservation);
        
        return response()->json([
            'clientSecret' => $paymentIntent->client_secret,
        ]);
    }

    public function handleSuccess(Request $request)
    {
        $validated = $request->validate([
            'payment_intent_id' => 'required|string',
        ]);
        
        $success = $this->paymentService->handlePaymentSuccess($validated['payment_intent_id']);
        
        if (!$success) {
            return response()->json(['message' => 'Payment processing failed'], 400);
        }
        
        return response()->json(['message' => 'Payment processed successfully']);
    }
}