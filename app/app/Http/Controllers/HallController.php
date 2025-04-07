<?php

namespace App\Http\Controllers;

use App\Models\Hall;
use App\Models\Seat;
use Illuminate\Http\Request;
use App\Repositories\Contracts\HallRepositoryInterface;

class HallController extends Controller
{
    private $hallRepository;

    public function __construct(HallRepositoryInterface $hallRepository)
{
    $this->hallRepository = $hallRepository;
    $this->middleware('auth:api')->except(['index', 'show']);
    //$this->middleware('admin')->except(['index', 'show']);
    // Retirez la ligne avec le middleware admin
}

    /**
     * Liste toutes les salles
     */
    public function index()
    {
        $halls = $this->hallRepository->all();
        return response()->json($halls);
    }

    /**
     * Affiche une salle spécifique avec ses sièges
     */
    public function show($id)
    {
        $hall = $this->hallRepository->findWithSeats($id);
        return response()->json($hall);
    }

    /**
     * Crée une nouvelle salle (Admin seulement)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'capacity' => 'required|integer',
            'type' => 'required|in:normal,vip,imax,4dx',
            'seat_map' => 'nullable|json'
        ]);

        $hall = $this->hallRepository->create($validated);
        return response()->json($hall, 201);
    }

    /**
     * Met à jour une salle (Admin seulement)
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'capacity' => 'sometimes|integer',
            'type' => 'sometimes|in:normal,vip,imax,4dx',
            'seat_map' => 'nullable|json'
        ]);

        $hall = $this->hallRepository->update($id, $validated);
        return response()->json($hall);
    }

    /**
     * Supprime une salle (Admin seulement)
     */
    public function destroy($id)
    {
        $this->hallRepository->delete($id);
        return response()->json(null, 204);
    }

    /**
     * Configure les sièges d'une salle (Admin seulement)
     */
    public function configureSeats(Request $request, $hallId)
    {
        $validated = $request->validate([
            'seats' => 'required|array',
            'seats.*.row' => 'required|string|max:2',
            'seats.*.number' => 'required|integer',
            'seats.*.type' => 'required|in:normal,couple,handicap'
        ]);

        $hall = $this->hallRepository->configureSeats($hallId, $validated['seats']);
        return response()->json($hall);
    }

    /**
     * Liste les séances pour une salle
     */
    public function screenings($hallId)
    {
        $screenings = $this->hallRepository->getScreenings($hallId);
        return response()->json($screenings);
    }
}