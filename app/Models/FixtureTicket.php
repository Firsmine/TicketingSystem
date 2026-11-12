<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FixtureTicket extends Model
{
    protected $fillable = [
        'fixture_id',
        'stadium_section_id',
        'currency',
        'price',
        'quota',
        'remaining_quota'
    ];
    public function booking()
    {
        return $this->hasMany(Booking::class, 'fixture_ticket_id');
    }
    public function fixture()
    {
        return $this->belongsTo(Fixture::class, 'fixture_id');
    }
    public function stadium_section()
    {
        return $this->belongsTo(StadiumSection::class, 'stadium_section_id');
    }
}
