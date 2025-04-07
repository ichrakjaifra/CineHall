<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Ticket extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'seat_id',
        'price',
        'qr_code'
    ];

    protected $casts = [
        'price' => 'decimal:2'
    ];

    // Relations
    public function reservation(): BelongsTo
    {
        return $this->belongsTo(Reservation::class);
    }

    public function seat(): BelongsTo
    {
        return $this->belongsTo(Seat::class);
    }

    // Méthodes
    public function generateQrCode(): string
    {
        $this->qr_code = 'TICKET-' . $this->id . '-' . bin2hex(random_bytes(8));
        $this->save();
        
        return $this->qr_code;
    }

    public function isCoupleSeat(): bool
    {
        return $this->seat->type === 'couple';
    }
}