<?php

namespace App\Repositories\Eloquent;

use App\Models\Screening;
use App\Repositories\Contracts\ScreeningRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class ScreeningRepository implements ScreeningRepositoryInterface
{
    protected $model;

    public function __construct(Screening $model)
    {
        $this->model = $model;
    }

    public function all(): Collection
    {
        return $this->model->with(['movie', 'hall'])->get();
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->with(['movie', 'hall'])->paginate($perPage);
    }

    public function find(int $id): ?Screening
    {
        return $this->model->with(['movie', 'hall'])->find($id);
    }

    public function create(array $data): Screening
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): bool
    {
        return $this->model->findOrFail($id)->update($data);
    }

    public function delete(int $id): bool
    {
        return $this->model->findOrFail($id)->delete();
    }

    public function filterByType(string $type): Collection
    {
        return $this->model->where('type', $type)
            ->with(['movie', 'hall'])
            ->get();
    }

    public function upcomingForMovie(int $movieId): Collection
    {
        return $this->model->where('movie_id', $movieId)
            ->where('start_time', '>', now())
            ->with(['movie', 'hall'])
            ->orderBy('start_time')
            ->get();
    }

    public function findByHall(int $hallId): Collection
    {
        return $this->model->where('hall_id', $hallId)
            ->with(['movie'])
            ->get();
    }

    public function checkTimeConflict(int $hallId, string $startTime, string $endTime, ?int $ignoreId = null): bool
    {
        $query = $this->model->where('hall_id', $hallId)
            ->where(function($q) use ($startTime, $endTime) {
                $q->whereBetween('start_time', [$startTime, $endTime])
                  ->orWhereBetween('end_time', [$startTime, $endTime])
                  ->orWhere(function($q) use ($startTime, $endTime) {
                      $q->where('start_time', '<', $startTime)
                        ->where('end_time', '>', $endTime);
                  });
            });

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->exists();
    }

    public function search(array $criteria): Collection
    {
        $query = $this->model->with(['movie', 'hall']);

        if (isset($criteria['movie_id'])) {
            $query->where('movie_id', $criteria['movie_id']);
        }

        if (isset($criteria['hall_id'])) {
            $query->where('hall_id', $criteria['hall_id']);
        }

        if (isset($criteria['type'])) {
            $query->where('type', $criteria['type']);
        }

        if (isset($criteria['language'])) {
            $query->where('language', $criteria['language']);
        }

        if (isset($criteria['from_date'])) {
            $query->where('start_time', '>=', $criteria['from_date']);
        }

        if (isset($criteria['to_date'])) {
            $query->where('end_time', '<=', $criteria['to_date']);
        }

        return $query->get();
    }

    public function query()
{
    return Screening::query();
}
}