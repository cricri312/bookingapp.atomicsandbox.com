<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Room;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $bookings = $request->user()
            ->bookings()
            ->with('room')
            ->latest()
            ->get();

        return response()->json($bookings);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'room_id'            => 'required|exists:rooms,id',
            'starts_at'          => 'required|date|after:now',
            'ends_at'            => 'required|date|after:starts_at',
            'participants_count' => 'required|integer|min:1',
        ], [
            'room_id.required'            => 'Pokój jest wymagany.',
            'room_id.exists'              => 'Wybrany pokój nie istnieje.',
            'starts_at.required'          => 'Data rozpoczęcia jest wymagana.',
            'starts_at.after'             => 'Data rozpoczęcia musi być w przyszłości.',
            'ends_at.required'            => 'Data zakończenia jest wymagana.',
            'ends_at.after'               => 'Data zakończenia musi być późniejsza niż data rozpoczęcia.',
            'participants_count.required' => 'Liczba uczestników jest wymagana.',
            'participants_count.integer'  => 'Liczba uczestników musi być liczbą całkowitą.',
            'participants_count.min'      => 'Minimalna liczba uczestników to 1.',
        ]);

        $room = Room::findOrFail($data['room_id']);

        if ($data['participants_count'] > $room->capacity) {
            return response()->json([
                'message' => 'Liczba uczestników przekracza pojemność pokoju.',
                'errors'  => ['participants_count' => ['Liczba uczestników przekracza pojemność pokoju.']],
            ], 422);
        }

        $conflict = Booking::where('room_id', $data['room_id'])
            ->whereIn('status', ['pending', 'confirmed'])
            ->where('starts_at', '<', $data['ends_at'])
            ->where('ends_at', '>', $data['starts_at'])
            ->exists();

        if ($conflict) {
            return response()->json([
                'message' => 'Pokój jest już zarezerwowany w tym terminie.',
                'errors'  => ['starts_at' => ['Pokój jest już zarezerwowany w tym terminie.']],
            ], 422);
        }

        $booking = Booking::create([
            'room_id'            => $data['room_id'],
            'user_id'            => $request->user()->id,
            'starts_at'          => $data['starts_at'],
            'ends_at'            => $data['ends_at'],
            'participants_count' => $data['participants_count'],
            'status'             => 'pending',
        ]);

        return response()->json($booking->load('room'), 201);
    }

    public function cancel(Request $request, Booking $booking)
    {
        if ($booking->user_id !== $request->user()->id) {
            return response()->json(['message' => 'Brak dostępu.'], 403);
        }

        if ($booking->status === 'cancelled') {
            return response()->json(['message' => 'Rezerwacja jest już anulowana.'], 422);
        }

        $booking->update(['status' => 'cancelled']);

        return response()->json($booking);
    }
}
