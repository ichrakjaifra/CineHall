<?php

namespace App\Repositories\Contracts;

interface StatsRepositoryInterface
{
    public function getOverviewStats();
    public function getMoviesRanking();
    public function getOccupancyRates();
    public function getRevenueByMovie();
    public function getRecentReservations($limit = 10);
}