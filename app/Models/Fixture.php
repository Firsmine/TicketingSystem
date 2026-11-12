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
    protected $hidden = [
        'created_at',
        'updated_at'
    ];
    public function stadium()
    {
        return $this->belongsTo(Stadium::class, 'stadium_id');
    }
    public function homeTeam()
    {
        return $this->belongsTo(Team::class, 'home_team_id');
    }
    public function awayTeam()
    {
        return $this->belongsTo(Team::class, 'away_team_id');
    }
    public function fixtureTicket()
    {
        return $this->hasMany(FixtureTicket::class, 'fixture_id');
    }
}
