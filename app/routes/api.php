<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HallController;
use App\Http\Controllers\MovieController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ScreeningController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\Admin\DashboardController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::get('/screenings', [ScreeningController::class, 'index']);

Route::middleware('auth:api')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/refresh', [AuthController::class, 'refresh']);
    Route::get('/user-profile', [AuthController::class, 'userProfile']);

    Route::put('/profile', [AuthController::class, 'updateProfile']);
    Route::delete('/profile', [AuthController::class, 'deleteAccount']);

    // Movies routes
    Route::get('/movies', [MovieController::class, 'index']);
    Route::get('/movies/{id}', [MovieController::class, 'show']);
    Route::get('/movies/{id}/screenings', [MovieController::class, 'upcomingScreenings']);
    Route::get('/movies/search', [MovieController::class, 'search']);

    Route::post('/movies', [MovieController::class, 'store']);
        Route::put('/movies/{id}', [MovieController::class, 'update']);
         Route::delete('/movies/{id}', [MovieController::class, 'destroy']);

         Route::post('/screenings', [ScreeningController::class, 'store']);
    Route::put('/screenings/{id}', [ScreeningController::class, 'update']);
    Route::delete('/screenings/{id}', [ScreeningController::class, 'destroy']);

    Route::get('/halls', [HallController::class, 'index']);
Route::get('/halls/{id}', [HallController::class, 'show']);

Route::post('/halls', [HallController::class, 'store']);
    Route::put('/halls/{id}', [HallController::class, 'update']);
    Route::delete('/halls/{id}', [HallController::class, 'destroy']);
    Route::post('/halls/{id}/seats', [HallController::class, 'configureSeats']);
    
    // Admin-only routes
    Route::middleware('admin')->group(function () {
        // Route::post('/movies', [MovieController::class, 'store']);
        // Route::put('/movies/{id}', [MovieController::class, 'update']);
        // Route::delete('/movies/{id}', [MovieController::class, 'destroy']);

        // Gestion des séances
    // Route::post('/screenings', [ScreeningController::class, 'store']);
    // Route::put('/screenings/{id}', [ScreeningController::class, 'update']);
    // Route::delete('/screenings/{id}', [ScreeningController::class, 'destroy']);

    // Route::post('/halls', [HallController::class, 'store']);
    // Route::put('/halls/{id}', [HallController::class, 'update']);
    // Route::delete('/halls/{id}', [HallController::class, 'destroy']);
    // Route::post('/halls/{id}/seats', [HallController::class, 'configureSeats']);
    });

    // Reservation routes
    Route::get('/reservations', [ReservationController::class, 'index']);
    Route::post('/reservations', [ReservationController::class, 'store']);
    Route::get('/reservations/{id}', [ReservationController::class, 'show']);
    Route::post('/reservations/{id}/cancel', [ReservationController::class, 'cancel']);
    Route::post('/reservations/{id}/confirm-payment', [ReservationController::class, 'confirmPayment']);

    // Ticket routes
    Route::get('/tickets/{ticketId}/download', [TicketController::class, 'downloadTicket']);
    Route::get('/reservations/{reservationId}/download', [TicketController::class, 'downloadReservation']);
    Route::post('/tickets/validate', [TicketController::class, 'validateTicket']);

    // Payment routes
    Route::post('/reservations/{reservationId}/create-payment-intent', [PaymentController::class, 'createPaymentIntent']);
    Route::post('/payments/success', [PaymentController::class, 'handleSuccess']);

    Route::prefix('admin')->middleware(['auth:api', 'admin'])->group(function () {
      Route::get('/dashboard/overview', [DashboardController::class, 'overview']);
      Route::get('/dashboard/movies-ranking', [DashboardController::class, 'moviesRanking']);
      Route::get('/dashboard/occupancy-rates', [DashboardController::class, 'occupancyRates']);
      Route::get('/dashboard/revenue-by-movie', [DashboardController::class, 'revenueByMovie']);
      Route::get('/dashboard/recent-reservations', [DashboardController::class, 'recentReservations']);
  });

});