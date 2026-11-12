<?php

namespace App\Jobs;

use App\Http\Controllers\FixtureController;
use App\Models\Booking;
use App\Models\FixtureTicket;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\DB;

class ExpireBooking implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        DB::transaction(function () {
            $booking = Booking::where('id', $this->bookingId)->lockForUpdate()->first();

            if (!$booking || $booking->payment_status !== 'PENDING') {
                return;
            }
            if (now()->lt($booking->payment_expired_at)) {
                return;
            }
            $ticket = FixtureTicket::where('id', $booking->fixture_ticket_id)->lockForUpdate()->first();
            $ticket->increment('remaining_quota', $booking->quantity);
            $booking->update(['payment_status' => 'EXPIRED']);
        });
    }
}
