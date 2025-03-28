<?php

namespace App\Http\Middleware;

use Log;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Utilisez cette façade au lieu du contrat

class AdminMiddleware
{
    
  public function handle(Request $request, Closure $next)
  {
      $user = auth()->user();
  
      if (!$user) {
          return response()->json(['message' => 'Connectez-vous d\'abord'], 401);
      }
  
      // Vérification souple (vous pouvez aussi vérifier $user->role === 'admin')
      if ($user->email !== 'admin@example.com') {
          return response()->json(['message' => 'Réservé à l\'administrateur'], 403);
      }
  
      return $next($request);
  }
}