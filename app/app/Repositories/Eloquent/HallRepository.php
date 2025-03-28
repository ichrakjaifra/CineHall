<?php

namespace App\Repositories;

use App\Models\Hall;
use App\Models\Seat;
use App\Repositories\Contracts\HallRepositoryInterface;

class HallRepository implements HallRepositoryInterface
{
    public function all()
    {
        return Hall::all();
    }

    public function find($id)
    {
        return Hall::findOrFail($id);
    }

    public function findWithSeats($id)
    {
        return Hall::with('seats')->findOrFail($id);
    }

    public function create(array $data)
    {
        return Hall::create($data);
    }

    public function update($id, array $data)
    {
        $hall = Hall::findOrFail($id);
        $hall->update($data);
        return $hall;
    }

    public function delete($id)
    {
        $hall = Hall::findOrFail($id);
        $hall->delete();
    }

    public function configureSeats($hallId, array $seats)
    {
        // Supprime les anciens sièges
        Seat::where('hall_id', $hallId)->delete();
        
        // Crée les nouveaux sièges
        foreach ($seats as $seat) {
            Seat::create([
                'hall_id' => $hallId,
                'row' => $seat['row'],
                'number' => $seat['number'],
                'type' => $seat['type']
            ]);
        }
        
        return $this->findWithSeats($hallId);
    }

    public function getScreenings($hallId)
    {
        return Hall::findOrFail($hallId)
                 ->screenings()
                 ->with('movie')
                 ->where('start_time', '>', now())
                 ->orderBy('start_time')
                 ->get();
    }
}