@extends('layouts.app')

@section('content')
    <h1>Your Bookings</h1>
    <ul>
        @foreach($bookings as $booking)
            <li>
                {{ $booking->car->make }} {{ $booking->car->model }} ({{ $booking->pickup_date }} - {{ $booking->return_date }}) - Status: {{ $booking->status }}
                @if($booking->status == 'pending')
                    <form action="{{ route('bookings.cancel', $booking->id) }}" method="POST">
                        @csrf
                        <button type="submit">Cancel</button>
                    </form>
                @endif
            </li>
        @endforeach
    </ul>
@endsection
