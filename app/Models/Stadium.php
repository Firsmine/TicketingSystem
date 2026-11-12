<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stadium extends Model
{
    protected $fillable = [
        'name',
        'city',
        'capacity'
    ];

    public function stadiumSection()
    {
        return $this->hasMany(StadiumSection::class, 'stadium_id');
    }

    public function fixture()
    {
        return $this->hasMany(Fixture::class, 'stadium_id');
    }
}
