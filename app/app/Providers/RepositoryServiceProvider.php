<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(
            \App\Repositories\Contracts\AuthRepositoryInterface::class,
            \App\Repositories\Eloquent\AuthRepository::class
        );
        
        $this->app->bind(
            \App\Repositories\Contracts\MovieRepositoryInterface::class,
            \App\Repositories\Eloquent\MovieRepository::class
        );
        
        // Ajoutez cette ligne pour le ReservationRepository
        $this->app->bind(
            \App\Repositories\Contracts\ReservationRepositoryInterface::class,
            \App\Repositories\Eloquent\ReservationRepository::class
        );
        
        // Ajoutez aussi ces bindings si vous les utilisez
        $this->app->bind(
            \App\Repositories\Contracts\AuthRepositoryInterface::class,
            \App\Repositories\Eloquent\AuthRepository::class
        );
        
        $this->app->bind(
            \App\Repositories\Contracts\TicketRepositoryInterface::class,
            \App\Repositories\Eloquent\TicketRepository::class
        );

        $this->app->bind(
          \App\Repositories\Contracts\ScreeningRepositoryInterface::class,
          \App\Repositories\Eloquent\ScreeningRepository::class
      );
    }
}