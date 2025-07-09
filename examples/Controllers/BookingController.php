<?php

namespace Examples\Http\Controllers;

use Illuminate\Http\Request;
use RouteDocs\Attributes\delete;
use RouteDocs\Attributes\formParam;
use RouteDocs\Attributes\get;
use RouteDocs\Attributes\param;
use RouteDocs\Attributes\post;
use RouteDocs\Attributes\queryParam;

class BookingController
{
    #[get(path: '/bookings', name: 'bookings.index')]
    #[queryParam(key: 'dateStart', cast: 'date', description: 'Filter bookings by date')]
    #[queryParam(key: 'dateEnd', cast: 'date', description: 'Filter bookings by date')]
    //#[returns(Booking::class)] coming soon
    public function index()
    {
        // Return a list of bookings
    }

    #[post('/bookings', name: 'bookings.store')]
    #[formParam('dateStart', 'date', required: false, description: 'Start date for the booking')]
    #[formParam('dateEnd', 'date', required: false, description: 'End date for the booking')]
    public function store(Request $request)
    {
        // Validate and create a new booking
    }

    #[get('/bookings/{id}', name: 'bookings.show')]
    #[param('path', key: 'id', cast: 'int', required: true, description: 'Booking ID')]
    #[param('query', key: 'hydrate', cast: 'bool', required: false, description: 'Hydrate the booking details')]
    public function show(int $id)
    {
        // Return details for a single booking
    }

    #[delete(path: '/bookings/{id}/cancel', name: 'bookings.cancel')]
    public function cancel(int $id)
    {
        // Cancel a booking
    }

    #[get(path: '/bookings/stats/{frequency}', name: 'bookings.stats')]
    #[param('path', key: 'frequency', cast: 'string', required: true, description: 'Frequency of the stats (daily, weekly, monthly)', example: 'daily|weekly|monthly')]
    public function stats()
    {
        // Return daily booking statistics
    }
}
