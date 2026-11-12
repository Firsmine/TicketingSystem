<?php

namespace App\Http\Controllers;

use App\Jobs\ExpireBooking;
use App\Models\Booking;
use App\Models\FixtureTicket;
use Auth;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class BookingController extends Controller
{

    public function index()
    {
        $booking = Booking::with('fixtureTicket.fixture.homeTeam', 'fixtureTicket.fixture.awayTeam', 'fixtureTicket.fixture.stadium', 'fixtureTicket.stadiumSection')->where('user_id', Auth::id())->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Bookings retrieved successfully',
            'data' => ['bookings' => $booking]
        ]);
    }

    public function store(Request $request)
    {
        $data = Validator::make($request->all(), [
            'fixture_ticket_id' => 'required|integer|exists:fixture_tickets,id',
            'quantity' => 'required|integer|min:1'
        ]);

        if ($data->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid request data',
                'errors' => $data->errors()
            ], 422);
        }

        $booking = DB::transaction(function () use ($request, $data) {
            $ticket = FixtureTicket::where('id', $data['fixture_ticket_id'])->lockForUpdate()->first();

            if ($data['quantity'] > $ticket->remaining_quota) {
                return null;
            }

            $subtotal = $ticket->price * $data['quantity'];
            $serviceFee = $subtotal * 0.5;
            $totalAmount = $subtotal + $serviceFee;

            $booking = Booking::create([
                'user_id' => Auth::id(),
                'fixture_ticket_id' => $request->fixture_ticket_id,
                'currency' => $ticket->currency,
                'quantity' => $data['quantity'],
                'subtotal_amount' => $subtotal,
                'service_fee' => $serviceFee,
                'total_amount' => $totalAmount,
                'payment_status' => 'PENDING',
                'payment_expired_at' => now()->addSeconds(30)
            ]);

            $booking->booking_reference = 'BK-' . $booking->created_at->format('ymd') . '-' . str_pad($booking->id, 4, '0', STR_PAD_LEFT);
            $booking->save();

            $ticket->decrement('remaining_quota', data['quantity']);
            return $booking;
        });

        if (!$booking) {
            return response()->json([
                'status' => 'error',
                'message' => 'Not enough quota available'
            ], 409);
        }
        ExpireBooking::dispatch($booking->id)->delay($booking->payment_expired_at);

        return response()->json([
            'status' => 'success',
            'message' => 'Booking created successfully',
            'data' => ['booking' => $booking]
        ]);
    }

    public function show(string $id)
    {
        $booking = Booking::with('fixtureTicket.fixture.homeTeam', 'fixtureTicket.fixture.awayTeam', 'fixtureTicket.fixture.stadium', 'fixtureTicket.stadiumSection')->where('user_id', Auth::id())->findOrFail($id);

        return response()->json([
            'status' => 'success',
            'message' => 'Booking details retrieved successfully',
            'data' => ['booking' => $booking]
        ]);
    }

    public function payment(Request $request, string $id)
    {
        $data = $request->validate([
            'booking_reference' => 'required|string|exists:bookings,booking_reference',
            'payment_method' => 'required|string|max:255',
            'payment_paid_at' => 'required|date'
        ]);

        $result = DB::transaction(function () use ($data) {
            $booking = Booking::where('booking_reference', $data['booking_reference'])->lockForUpdate()->first();

            if ($booking->payment_status === 'PAID') {
                return ['type' => 'paid', 'booking' => $booking];
            }
            if ($booking->payment_status !== 'PENDING') {
                return ['type' => 'expired'];
            }
            $paidAt = Carbon::parse($data['payment_paid_at']);

            if ($paidAt->gt($booking->payment_expired_at)) {
                $ticket = FixtureTicket::where('id', $booking->fixture_ticket_id)->lockForUpdate()->first();
                $ticket->increment('remaining_quota', $booking->quantity);
                $booking->update(['payment_status' => 'EXPIRED']);
                return ['type' => 'expired'];
            }
            $booking->update([
                'payment_status' => 'PAID',
                'payment_method' => $data['payment_method'],
                'payment_paid_at' => $paidAt
            ]);
            return ['type' => 'paid', 'booking' => $booking->fresh()];
        });
        if ($result['type'] === 'expired') {
            return response()->json([
                'status' => 'error',
                'message' => 'Booking has expired'
            ], 409);
        }
        return response()->json([
            'status' => 'success',
            'message' => 'Webhook triggered successfully',
            'data' => ['booking' => $result['booking']]
        ], 200);
    }
}
