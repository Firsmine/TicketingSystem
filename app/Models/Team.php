<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    protected $fillable = [
        'name',
        'code',
        'flag'
    ];
    public function fixture()
    {
        return $this->hasMany(Fixture::class);
    }
}
