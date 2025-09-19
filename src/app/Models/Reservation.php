<?php

namespace App\Models;

use App\Models\Review;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'shop_id',
        'date',
        'time',
        'number',
        'checkin_token',
        'visited',
        'reminded',
        'paid',
    ];

    protected $casts = [
        'date' => 'date',
        'time' => 'datetime:H:i',
        'visited' => 'boolean',
        'reminded' => 'boolean',
        'paid' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function shop()
    {
        return $this->belongsTo(Shop::class);
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }

    protected static function booted()
    {
        static::creating(function ($reservation) {
            $reservation->checkin_token = Str::uuid();
        });
    }
}
