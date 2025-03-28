<?php

namespace App\Repositories\Contracts;

use App\Models\Ticket;

interface TicketRepositoryInterface
{
    public function generateTicketPdf(int $ticketId);
    public function generateReservationPdf(int $reservationId);
    public function validateTicket(string $qrCode): bool;
}