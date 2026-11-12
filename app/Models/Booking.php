<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $fillable = [
        'user_id',
        'fixture_ticket_id',
        'booking_reference',
        'currency',
        'quantity',
        'subtotal_amount',
        'service_fee',
        'total_amount',
        'payment_status',
        'payment_expired_at',
        'payment_paid_at',
        'payment_method',
    ];
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function fixtureTicket()
    {
        return $this->belongsTo(FixtureTicket::class, 'fixture_ticket_id');
    }
}
