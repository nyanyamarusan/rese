<?php

namespace App\Models;

use App\Models\Area;
use App\Models\Genre;
use App\Models\Owner;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shop extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'area_id',
        'genre_id',
        'owner_id',
        'open_time',
        'close_time',
        'detail',
        'image',
    ];

    protected $casts = [
        'open_time' => 'datetime:H:i',
        'close_time' => 'datetime:H:i',
    ];

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function genre()
    {
        return $this->belongsTo(Genre::class);
    }

    public function likes()
    {
        return $this->belongsToMany(User::class, 'likes');
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public function owner()
    {
        return $this->belongsTo(Owner::class);
    }

    public function scopeAreaSearch($query, $area_id)
    {
        if (!empty($area_id)) {
            $query->where('area_id', $area_id);
        }
    }

    public function scopeGenreSearch($query, $genre_id)
    {
        if (!empty($genre_id)) {
            $query->where('genre_id', $genre_id);
        }
    }

    public function scopeKeywordSearch($query, $keyword)
    {
        if (!empty($keyword)) {
            $query->where('name', 'like', '%' . $keyword . '%');
        }
    }

    public function isLikedBy(User $user): bool
    {
        return $user->likes()->where('shop_id', $this->id)->exists();
    }
}
