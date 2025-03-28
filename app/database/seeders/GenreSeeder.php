<?php

namespace Database\Seeders;

use App\Models\Genre;
use Illuminate\Database\Seeder;

class GenreSeeder extends Seeder
{
    public function run()
    {
        $genres = [
            ['name' => 'Action'],
            ['name' => 'Comédie'],
            ['name' => 'Drame'],
            ['name' => 'Science-Fiction'],
            ['name' => 'Thriller'],
            ['name' => 'Horreur'],
            ['name' => 'Romance'],
            ['name' => 'Animation'],
        ];

        foreach ($genres as $genre) {
            Genre::create($genre);
        }
    }
}