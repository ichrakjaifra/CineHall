<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\Contracts\HallRepositoryInterface;
use App\Repositories\Eloquent\HallRepository; // Modifié ici

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(
            HallRepositoryInterface::class,
            HallRepository::class // Modifié ici
        );
    }

    public function boot(): void
    {
        //
    }
}