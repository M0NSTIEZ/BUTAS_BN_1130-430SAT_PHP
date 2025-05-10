@extends('layouts.app')

@section('content')
    <h1>Available Cars for Booking</h1>
    <ul>
        @foreach($cars as $car)
            <li>
                {{ $car->make }} {{ $car->model }} - ${{ $car->price_per_day }} per day
                <form action="{{ url('/bookings') }}" method="POST">
                    @csrf
                    <input type="hidden" name="car_id" value="{{ $car->id }}">
                    <input type="datetime-local" name="pickup_date" required>
                    <input type="datetime-local" name="return_date" required>
                    <button type="submit">Book Car</button>
                </form>
            </li>
        @endforeach
    </ul>
@endsection