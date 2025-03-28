<?php

namespace App\Console\Commands;

use App\Repositories\Contracts\ReservationRepositoryInterface;
use Illuminate\Console\Command;

class CancelExpiredReservations extends Command
{
    protected $signature = 'reservations:cancel-expired';
    protected $description = 'Cancel expired reservations';

    private $reservationRepository;

    public function __construct(ReservationRepositoryInterface $reservationRepository)
    {
        parent::__construct();
        $this->reservationRepository = $reservationRepository;
    }

    public function handle()
    {
        $this->reservationRepository->cancelExpiredReservations();
        $this->info('Expired reservations have been cancelled.');
    }
}