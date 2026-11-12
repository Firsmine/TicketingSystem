<?php

namespace App\Http\Controllers;

use App\Models\Fixture;

class FixtureController extends Controller
{
    public function index()
    {
        $fixtures = Fixture::with('homeTeam', 'awayTeam', 'stadium')->get();

        $data = $fixtures->map(function ($fixture) {
            return [
                'id' => $fixture->id,
                'stage' => $fixture->stage,
                'match_datetime' => $fixture->match_datetime,

                'home_time' => [
                    'id' => $fixture->homeTeam->id,
                    'name' => $fixture->homeTeam->name,
                    'code' => $fixture->homeTeam->code,
                    'flag' => $fixture->homeTeam->flag,
                ],

                'away_time' => [
                    'id' => $fixture->awayTeam->id,
                    'name' => $fixture->awayTeam->name,
                    'code' => $fixture->awayTeam->code,
                    'flag' => $fixture->awayTeam->flag,
                ],
                'stadium' => [
                    'id' => $fixture->stadium->id,
                    'name' => $fixture->stadium->name,
                    'city' => $fixture->stadium->city,
                    'capacity' => $fixture->stadium->capacity,
                ],
            ];
        });

        return response()->json([
            'status' => 'success',
            'message' => 'Fixtures retrieved successfully',
            'data' => $data
        ], 200);
    }

    public function show($id)
    {
        $fixture = Fixture::with('fixtureTicket.stadiumSection', 'homeTeam', 'awayTeam', 'stadium')->findOrFail($id);
        $data = [
            'id' => $fixture->id,
            'stage' => $fixture->stage,
            'match_datetime' => $fixture->match_datetime,

            'fixture_tickets' => $fixture->fixtureTicket->map(function ($ticket) {
                return [
                    'id' => $ticket->id,
                    'currency' => $ticket->currency,
                    'price' => $ticket->price,
                    'quota' => $ticket->quota,
                    'remaining_quota' => $ticket->remaining_quota,
                    'stadium_section' => [
                        'id' => $ticket->stadiumSection->id,
                        'name' => $ticket->stadiumSection->name,
                        'capacity' => $ticket->stadiumSection->capacity,
                    ]
                ];
            }),

            'home_time' => [
                'id' => $fixture->homeTeam->id,
                'name' => $fixture->homeTeam->name,
                'code' => $fixture->homeTeam->code,
                'flag' => $fixture->homeTeam->flag,
            ],

            'away_time' => [
                'id' => $fixture->awayTeam->id,
                'name' => $fixture->awayTeam->name,
                'code' => $fixture->awayTeam->code,
                'flag' => $fixture->awayTeam->flag,
            ],
            'stadium' => [
                'id' => $fixture->stadium->id,
                'name' => $fixture->stadium->name,
                'city' => $fixture->stadium->city,
                'capacity' => $fixture->stadium->capacity,
            ],
        ];

        return response()->json([
            'status' => 'success',
            'message' => 'Fixtures detail retrieved successfully',
            'data' => ['fixture' => $data]
        ]);
    }
}
