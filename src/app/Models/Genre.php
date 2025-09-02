<?php

namespace App\Models;

use App\Models\Shop;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    protected $fillable = [
        'name',
    ];

    public function shops()
    {
        return $this->hasMany(Shop::class);
    }
}
