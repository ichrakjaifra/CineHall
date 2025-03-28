<?php

namespace App\Http\Controllers;

use App\Repositories\Contracts\MovieRepositoryInterface;
use Illuminate\Http\Request;

class MovieController extends Controller
{
    private $movieRepository;

    public function __construct(MovieRepositoryInterface $movieRepository)
    {
        $this->movieRepository = $movieRepository;
    }

    public function index(Request $request)
    {
        $perPage = $request->get('per_page', 10);
        $movies = $this->movieRepository->paginate($perPage);
        return response()->json($movies);
    }

    public function show($id)
    {
        $movie = $this->movieRepository->find($id);
        return response()->json($movie);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'poster_url' => 'required|url',
            'trailer_url' => 'required|url',
            'duration_minutes' => 'required|integer',
            'min_age' => 'required|integer',
            'genres' => 'nullable|array',
            'genres.*' => 'exists:genres,id',
        ]);

        $movie = $this->movieRepository->create($validated);
        return response()->json($movie, 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'title' => 'sometimes|string|max:255',
            'description' => 'sometimes|string',
            'poster_url' => 'sometimes|url',
            'trailer_url' => 'sometimes|url',
            'duration_minutes' => 'sometimes|integer',
            'min_age' => 'sometimes|integer',
            'genres' => 'nullable|array',
            'genres.*' => 'exists:genres,id',
        ]);

        $movie = $this->movieRepository->update($id, $validated);
        return response()->json($movie);
    }

    public function destroy($id)
    {
        $this->movieRepository->delete($id);
        return response()->json(null, 204);
    }

    public function upcomingScreenings($id)
    {
        $screenings = $this->movieRepository->getUpcomingScreenings($id);
        return response()->json($screenings);
    }

    public function search(Request $request)
    {
        $query = $request->get('query');
        $movies = $this->movieRepository->search($query);
        return response()->json($movies);
    }
}