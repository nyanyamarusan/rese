<?php

namespace App\Models;

use App\Models\Reservation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'rating',
        'comment',
    ];

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }
}
