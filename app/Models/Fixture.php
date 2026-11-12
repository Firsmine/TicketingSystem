<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fixture extends Model
{
    protected $fillable = [
        'stage',
        'match_datetime',
        'home_team_id',
        'away_team_id',
        'stadium_id'
    ];
    public function stadium()
    {
        return $this->belongsTo(Stadium::class, 'stadium_id');
    }
    public function team()
    {
        return $this->belongsTo(Team::class);
    }
    public function fixtureTicket()
    {
        return $this->hasMany(FixtureTicket::class, 'fixture_id');
    }
}
