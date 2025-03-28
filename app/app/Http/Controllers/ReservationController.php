<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateReservationRequest;
use App\Repositories\Contracts\ReservationRepositoryInterface;
use Illuminate\Http\Request;

class ReservationController extends Controller
{
    private $reservationRepository;

    public function __construct(ReservationRepositoryInterface $reservationRepository)
    {
        $this->reservationRepository = $reservationRepository;
    }

    public function index(Request $request)
    {
        $user = $request->user();
        $reservations = $this->reservationRepository->getUserReservations($user->id);
        return response()->json($reservations);
    }

    public function store(CreateReservationRequest $request)
    {
        $data = $request->validated();
        $data['user_id'] = $request->user()->id;
        
        $reservation = $this->reservationRepository->create($data);
        
        return response()->json([
            'message' => 'Reservation created successfully',
            'reservation' => $this->reservationRepository->getReservationDetails($reservation->id),
            'expires_at' => $reservation->expires_at,
        ], 201);
    }

    public function show($id)
    {
        $reservation = $this->reservationRepository->getReservationDetails($id);
        
        if (!$reservation) {
            return response()->json(['message' => 'Reservation not found'], 404);
        }
        
        return response()->json($reservation);
    }

    public function cancel($id)
    {
        $success = $this->reservationRepository->update($id, ['status' => 'cancelled']);
        
        if (!$success) {
            return response()->json(['message' => 'Reservation not found'], 404);
        }
        
        return response()->json(['message' => 'Reservation cancelled successfully']);
    }

    public function confirmPayment(Request $request, $id)
    {
        $validated = $request->validate([
            'payment_method' => 'required|string|in:stripe,paypal',
            'transaction_id' => 'required|string',
        ]);
        
        $success = $this->reservationRepository->confirmPayment(
            $id,
            $validated['payment_method'],
            $validated['transaction_id']
        );
        
        if (!$success) {
            return response()->json(['message' => 'Reservation not found or already processed'], 400);
        }
        
        return response()->json([
            'message' => 'Payment confirmed and reservation completed',
            'reservation' => $this->reservationRepository->getReservationDetails($id),
        ]);
    }
}