<?php

namespace App\Repositories\Contracts;

use App\Models\Reservation;

interface ReservationRepositoryInterface
{
    public function create(array $data): Reservation;
    public function find(int $id): ?Reservation;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function getUserReservations(int $userId);
    public function confirmPayment(int $reservationId, string $paymentMethod, string $transactionId): bool;
    public function cancelExpiredReservations(): void;
    public function getReservationDetails(int $reservationId);
}