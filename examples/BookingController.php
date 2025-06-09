<?php

namespace Examples\Http\Controllers;

use RouteDocs\Attributes\get;
use RouteDocs\Attributes\post;
use Illuminate\Http\Request;

class BookingController
{
    #[get(path: '/bookings', name: 'bookings.index')]
    public function index()
    {
        // Return a list of bookings
    }

    #[post(path: '/bookings', name: 'bookings.store')]
    public function store(Request $request)
    {
        // Validate and create a new booking
    }

    #[get(path: '/bookings/{id}', name: 'bookings.show')]
    public function show(int $id)
    {
        // Return details for a single booking
    }

    #[post(path: '/bookings/{id}/cancel', name: 'bookings.cancel')]
    public function cancel(int $id)
    {
        // Cancel a booking
    }

    #[get(path: '/bookings/stats/daily', name: 'bookings.stats.daily')]
    public function dailyStats()
    {
        // Return daily booking statistics
    }
}
