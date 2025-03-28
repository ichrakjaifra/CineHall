<?php

namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\StatsRepositoryInterface;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StatsRepository implements StatsRepositoryInterface
{
    public function getOverviewStats()
    {
        return [
            'total_movies' => DB::table('movies')->count(),
            'total_screenings' => DB::table('screenings')
                ->where('start_time', '>', now()->subMonth())
                ->count(),
            'total_reservations' => DB::table('reservations')
                ->where('created_at', '>', now()->subMonth())
                ->count(),
            'total_revenue' => DB::table('payments')
                ->where('status', 'completed')
                ->where('created_at', '>', now()->subMonth())
                ->sum('amount'),
        ];
    }

    public function getMoviesRanking()
    {
        return DB::table('movies')
            ->leftJoin('screenings', 'movies.id', '=', 'screenings.movie_id')
            ->leftJoin('reservations', 'screenings.id', '=', 'reservations.screening_id')
            ->leftJoin('tickets', 'reservations.id', '=', 'tickets.reservation_id')
            ->select(
                'movies.id',
                'movies.title',
                DB::raw('COUNT(DISTINCT reservations.id) as reservations_count'),
                DB::raw('COUNT(tickets.id) as tickets_sold'),
                DB::raw('SUM(tickets.price) as revenue')
            )
            ->where('reservations.status', 'confirmed')
            ->where('reservations.created_at', '>', now()->subMonth())
            ->groupBy('movies.id', 'movies.title')
            ->orderByDesc('tickets_sold')
            ->limit(10)
            ->get();
    }

    public function getOccupancyRates()
    {
        return DB::table('screenings')
            ->join('halls', 'screenings.hall_id', '=', 'halls.id')
            ->leftJoin('reservations', function($join) {
                $join->on('screenings.id', '=', 'reservations.screening_id')
                     ->where('reservations.status', 'confirmed');
            })
            ->leftJoin('tickets', 'reservations.id', '=', 'tickets.reservation_id')
            ->select(
                'screenings.id',
                'screenings.start_time',
                'halls.name as hall_name',
                'halls.capacity',
                DB::raw('COUNT(tickets.id) as tickets_sold'),
                DB::raw('ROUND(COUNT(tickets.id) * 100.0 / halls.capacity, 2) as occupancy_rate')
            )
            ->where('screenings.start_time', '>', now()->subMonth())
            ->groupBy('screenings.id', 'screenings.start_time', 'halls.name', 'halls.capacity')
            ->orderByDesc('screenings.start_time')
            ->limit(20)
            ->get();
    }

    public function getRevenueByMovie()
    {
        return DB::table('movies')
            ->leftJoin('screenings', 'movies.id', '=', 'screenings.movie_id')
            ->leftJoin('reservations', 'screenings.id', '=', 'reservations.screening_id')
            ->leftJoin('tickets', 'reservations.id', '=', 'tickets.reservation_id')
            ->select(
                'movies.id',
                'movies.title',
                DB::raw('SUM(tickets.price) as revenue'),
                DB::raw('DATE_FORMAT(screenings.start_time, "%Y-%m") as month')
            )
            ->where('reservations.status', 'confirmed')
            ->where('screenings.start_time', '>', now()->subYear())
            ->groupBy('movies.id', 'movies.title', 'month')
            ->orderBy('month')
            ->orderByDesc('revenue')
            ->get()
            ->groupBy('month');
    }

    public function getRecentReservations($limit = 10)
    {
        return DB::table('reservations')
            ->join('users', 'reservations.user_id', '=', 'users.id')
            ->join('screenings', 'reservations.screening_id', '=', 'screenings.id')
            ->join('movies', 'screenings.movie_id', '=', 'movies.id')
            ->select(
                'reservations.id',
                'users.name as user_name',
                'movies.title as movie_title',
                'screenings.start_time',
                'reservations.status',
                'reservations.created_at'
            )
            ->orderByDesc('reservations.created_at')
            ->limit($limit)
            ->get();
    }
}