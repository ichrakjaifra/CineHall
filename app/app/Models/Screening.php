<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Screening extends Model
{
    use HasFactory;

    protected $fillable = [
      'movie_id',
      'hall_id',
      'start_time',
      'end_time',
      'language',
      'type'
  ];

  public function movie()
  {
      return $this->belongsTo(Movie::class);
  }

  public function hall()
  {
      return $this->belongsTo(Hall::class);
  }
}
