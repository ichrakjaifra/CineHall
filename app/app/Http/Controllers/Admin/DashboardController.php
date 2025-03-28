<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Repositories\Contracts\StatsRepositoryInterface;

class DashboardController extends Controller
{
    private $statsRepository;

    public function __construct(StatsRepositoryInterface $statsRepository)
    {
        $this->statsRepository = $statsRepository;
    }

    public function overview()
    {
        return response()->json($this->statsRepository->getOverviewStats());
    }

    public function moviesRanking()
    {
        return response()->json($this->statsRepository->getMoviesRanking());
    }

    public function occupancyRates()
    {
        return response()->json($this->statsRepository->getOccupancyRates());
    }

    public function revenueByMovie()
    {
        return response()->json($this->statsRepository->getRevenueByMovie());
    }

    public function recentReservations()
    {
        return response()->json($this->statsRepository->getRecentReservations());
    }
}