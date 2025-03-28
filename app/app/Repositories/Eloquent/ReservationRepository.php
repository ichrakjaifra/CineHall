<?php

namespace App\Repositories\Eloquent;

use App\Models\Reservation;
use App\Models\Seat;
use App\Models\Screening;
use App\Models\Ticket;
use App\Models\Payment;
use App\Repositories\Contracts\ReservationRepositoryInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReservationRepository implements ReservationRepositoryInterface
{
    public function create(array $data): Reservation
    {
        return DB::transaction(function () use ($data) {
            $reservation = Reservation::create([
                'user_id' => $data['user_id'],
                'screening_id' => $data['screening_id'],
                'expires_at' => Carbon::now()->addMinutes(15),
                'status' => 'pending',
            ]);

            foreach ($data['seats'] as $seatId) {
                $seat = Seat::findOrFail($seatId);
                
                Ticket::create([
                    'reservation_id' => $reservation->id,
                    'seat_id' => $seatId,
                    'price' => $this->calculateSeatPrice($seat, $reservation->screening),
                ]);
            }

            return $reservation;
        });
    }

    private function calculateSeatPrice(Seat $seat, Screening $screening): float
    {
        $basePrice = $screening->hall->type === 'vip' ? 150 : 100;
        return $seat->type === 'couple' ? $basePrice * 1.8 : $basePrice;
    }

    public function find(int $id): ?Reservation
    {
        return Reservation::with(['tickets', 'screening', 'user'])->find($id);
    }

    public function update(int $id, array $data): bool
    {
        $reservation = $this->find($id);
        return $reservation ? $reservation->update($data) : false;
    }

    public function delete(int $id): bool
    {
        $reservation = $this->find($id);
        return $reservation ? $reservation->delete() : false;
    }

    public function getUserReservations(int $userId)
    {
        return Reservation::where('user_id', $userId)
            ->with(['screening.movie', 'tickets.seat'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function confirmPayment(int $reservationId, string $paymentMethod, string $transactionId): bool
    {
        return DB::transaction(function () use ($reservationId, $paymentMethod, $transactionId) {
            $reservation = $this->find($reservationId);
            
            if (!$reservation || $reservation->status !== 'pending') {
                return false;
            }

            $totalAmount = $reservation->tickets->sum('price');

            Payment::create([
                'reservation_id' => $reservationId,
                'amount' => $totalAmount,
                'payment_method' => $paymentMethod,
                'transaction_id' => $transactionId,
                'status' => 'completed',
            ]);

            $reservation->update([
                'status' => 'confirmed',
                'expires_at' => null,
            ]);

            // Generate tickets with QR codes
            foreach ($reservation->tickets as $ticket) {
                $ticket->update([
                    'qr_code' => $this->generateQrCode($ticket),
                ]);
            }

            return true;
        });
    }

    private function generateQrCode(Ticket $ticket): string
    {
        // Implémentez la génération de QR code ici
        return 'QRCODE-' . $ticket->id . '-' . uniqid();
    }

    public function cancelExpiredReservations(): void
    {
        Reservation::where('status', 'pending')
            ->where('expires_at', '<', now())
            ->update(['status' => 'cancelled']);
    }

    public function getReservationDetails(int $reservationId)
    {
        return Reservation::with([
            'user',
            'screening.movie',
            'screening.hall',
            'tickets.seat',
            'payment'
        ])->find($reservationId);
    }
}