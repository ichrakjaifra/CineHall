<?php

namespace App\Repositories\Eloquent;

use App\Models\Movie;
use App\Repositories\Contracts\MovieRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class MovieRepository implements MovieRepositoryInterface
{
    protected $model;

    public function __construct(Movie $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->with('genres')->get();
    }

    public function paginate(int $perPage = 10): LengthAwarePaginator
    {
        return $this->model->with('genres')->paginate($perPage);
    }

    public function find(int $id)
    {
        return $this->model->with('genres')->findOrFail($id);
    }

    public function create(array $data)
    {
        $movie = $this->model->create($data);
        
        if (isset($data['genres'])) {
            $movie->genres()->sync($data['genres']);
        }
        
        return $movie;
    }

    public function update(int $id, array $data)
    {
        $movie = $this->find($id);
        $movie->update($data);
        
        if (isset($data['genres'])) {
            $movie->genres()->sync($data['genres']);
        }
        
        return $movie;
    }

    public function delete(int $id)
    {
        $movie = $this->find($id);
        return $movie->delete();
    }

    public function getUpcomingScreenings(int $movieId)
    {
        $movie = $this->find($movieId);
        return $movie->screenings()
            ->where('start_time', '>', now())
            ->orderBy('start_time')
            ->get();
    }

    public function search(string $query): Collection
    {
        return $this->model->where('title', 'like', "%$query%")
            ->orWhere('description', 'like', "%$query%")
            ->with('genres')
            ->get();
    }
}