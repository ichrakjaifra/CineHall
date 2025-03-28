<?php

namespace App\Repositories\Contracts;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface MovieRepositoryInterface
{
    public function all(): Collection;
    public function paginate(int $perPage = 10): LengthAwarePaginator;
    public function find(int $id);
    public function create(array $data);
    public function update(int $id, array $data);
    public function delete(int $id);
    public function getUpcomingScreenings(int $movieId);
    public function search(string $query): Collection;
}