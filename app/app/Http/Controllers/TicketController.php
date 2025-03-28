<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\TicketRepositoryInterface;
use Illuminate\Http\Request;

class TicketController extends Controller
{
    private $ticketRepository;

    public function __construct(TicketRepositoryInterface $ticketRepository)
    {
        $this->ticketRepository = $ticketRepository;
    }

    public function downloadTicket($ticketId)
    {
        $ticket = $this->ticketRepository->generateTicketPdf($ticketId);
        return response()->download(storage_path("app/public/tickets/{$ticket['filename']}"));
    }

    public function downloadReservation($reservationId)
    {
        $reservation = $this->ticketRepository->generateReservationPdf($reservationId);
        return response()->download(storage_path("app/public/reservations/{$reservation['filename']}"));
    }

    public function validateTicket(Request $request)
    {
        $validated = $request->validate([
            'qr_code' => 'required|string',
        ]);

        $isValid = $this->ticketRepository->validateTicket($validated['qr_code']);
        
        return response()->json([
            'valid' => $isValid,
        ]);
    }
}