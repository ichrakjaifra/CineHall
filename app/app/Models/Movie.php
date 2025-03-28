<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Movie extends Model
{
    use HasFactory;

    protected $fillable = [
      'title', 
      'description',
      'poster_url',
      'trailer_url',
      'duration_minutes',
      'min_age'
  ];

  public function genres()
  {
      return $this->belongsToMany(Genre::class, 'movie_genre');
  }

  public function screenings()
  {
      return $this->hasMany(Screening::class);
  }
}
