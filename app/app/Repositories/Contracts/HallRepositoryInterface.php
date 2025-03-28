<?php

namespace App\Repositories\Contracts;

interface HallRepositoryInterface
{
    public function all();
    public function find($id);
    public function findWithSeats($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function configureSeats($hallId, array $seats);
    public function getScreenings($hallId);
}