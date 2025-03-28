<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\ScreeningRepositoryInterface;
use Illuminate\Http\Request;

class ScreeningController extends Controller
{
    private $screeningRepository;

    public function __construct(ScreeningRepositoryInterface $screeningRepository)
    {
        $this->screeningRepository = $screeningRepository;
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'movie_id' => 'required|exists:movies,id',
            'hall_id' => 'required|exists:halls,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'language' => 'required|string',
            'type' => 'required|in:normal,vip'
        ]);

        $screening = $this->screeningRepository->create($validated);
        return response()->json($screening, 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'movie_id' => 'sometimes|exists:movies,id',
            'hall_id' => 'sometimes|exists:halls,id',
            'start_time' => 'sometimes|date',
            'end_time' => 'sometimes|date|after:start_time',
            'language' => 'sometimes|string',
            'type' => 'sometimes|in:normal,vip'
        ]);

        $screening = $this->screeningRepository->update($id, $validated);
        return response()->json($screening);
    }

    public function destroy($id)
    {
        $this->screeningRepository->delete($id);
        return response()->json(null, 204);
    }


    public function index(Request $request)
{
    $query = $this->screeningRepository->query();
    
    // Filtrage par type si spécifié
    if ($request->has('type')) {
        $query->where('type', $request->type);
    }
    
    // Filtrage par film si spécifié
    if ($request->has('movie_id')) {
        $query->where('movie_id', $request->movie_id);
    }
    
    // Chargement des relations
    $screenings = $query->with(['movie', 'hall'])
                      ->orderBy('start_time')
                      ->get();
    
    return response()->json($screenings);
}
}