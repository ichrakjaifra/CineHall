<?php

namespace App\Repositories\Eloquent;

use App\Models\Ticket;
use App\Models\Reservation;
use App\Repositories\Contracts\TicketRepositoryInterface;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class TicketRepository implements TicketRepositoryInterface
{
    public function generateTicketPdf(int $ticketId)
    {
        $ticket = Ticket::with(['reservation.user', 'reservation.screening.movie', 'seat'])->findOrFail($ticketId);
        
        $pdf = PDF::loadView('tickets.single', compact('ticket'));
        
        $filename = "ticket_{$ticketId}.pdf";
        Storage::put("public/tickets/{$filename}", $pdf->output());
        
        return [
            'path' => Storage::url("tickets/{$filename}"),
            'filename' => $filename,
        ];
    }

    public function generateReservationPdf(int $reservationId)
    {
        $reservation = Reservation::with(['user', 'screening.movie', 'tickets.seat'])->findOrFail($reservationId);
        
        $pdf = PDF::loadView('tickets.reservation', compact('reservation'));
        
        $filename = "reservation_{$reservationId}.pdf";
        Storage::put("public/reservations/{$filename}", $pdf->output());
        
        return [
            'path' => Storage::url("reservations/{$filename}"),
            'filename' => $filename,
        ];
    }

    public function validateTicket(string $qrCode): bool
    {
        $ticket = Ticket::where('qr_code', $qrCode)->first();
        
        if (!$ticket) {
            return false;
        }
        
        return $ticket->reservation->status === 'confirmed' && 
               !$ticket->is_used;
    }
}