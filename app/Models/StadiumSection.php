<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StadiumSection extends Model
{
    protected $fillable = [
        'stadium_id',
        'name',
        'capacity',
    ];
    public function fixtureTicket()
    {
        return $this->hasMany(FixtureTicket::class, 'stadium_section_id');
    }
    public function stadium()
    {
        return $this->belongsTo(Stadium::class, 'stadium_id');
    }
}
