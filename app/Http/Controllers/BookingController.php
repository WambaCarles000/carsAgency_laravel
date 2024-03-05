<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\User;
use Illuminate\Http\Request;

class BookingController extends Controller
{
    public function index(Request $request)
{
    $limit = $request->input('limit', 2); // Nombre d'éléments par page, par défaut 10.

    $bookings = Booking::paginate($limit);

    if ($bookings->isEmpty()) {
        return response(["message" => "No bookings found."], 404);
    }

    // Construire la réponse paginée
    $response_data = [
        'current_page' => $bookings->currentPage(),
        'limit' => $bookings->perPage(),
        'total_pages' => $bookings->lastPage(),
        'data' => $bookings->items(), // Les données réelles
    ];

    return response($response_data, 200);
}

    public function store(Request $request)
    {
        $bookingData = $request->validate([
            "start_date" => ["required", "date"],
            "end_date" => ["required", "date"],
            "user_id" => ["required", "numeric"],
            "car_id" => ["required", "numeric"],
        ]);

        // Vérification de l'autorisation
        if ($request->user()->id != $bookingData["user_id"]) {
            return response(["message" => "Prohibited"], 403);
        }

        $newBooking = Booking::create([
            "start_date" => $bookingData["start_date"],
            "end_date" => $bookingData["end_date"],
            "user_id" => $bookingData["user_id"],
            "car_id" => $bookingData["car_id"],
        ]);

        return response(['message' => 'New booking stored.', 'data' => $newBooking], 201);
    }

    public function show($id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response(["message" => "No booking found with id: $id"], 404);
        }

        return response($booking, 200);
    }

    public function update(Request $request, $id)
    {
        $bookingData = $request->validate([
            "start_date" => ["required", "date"],
            "end_date" => ["required", "date"],
            "user_id" => ["required", "numeric"],
            "car_id" => ["required", "numeric"],
        ]);

        $booking = Booking::find($id);

        if (!$booking) {
            return response(["message" => "No booking found with id: $id"], 404);
        }

        // Vérification de l'autorisation
        if ($request->user()->id != $booking->user_id) {
            return response(["message" => "Prohibited"], 403);
        }

        $booking->update($bookingData);

        return response(['message' => 'Booking updated.', 'data' => $booking], 200);
    }

    public function destroy(Request $request, $id)
    {
        $booking = Booking::find($id);

        if (!$booking) {
            return response(["message" => "No booking found with id: $id"], 404);
        }

        // Vérification de l'autorisation
        if ($request->user()->id != $booking->user_id) {
            return response(["message" => "Prohibited"], 403);
        }

        $booking->delete();

        return response(["message" => "Booking deleted successfully"], 200);
    }



///FONCTIONS DE RECHERCHE des reservations d'un utilisateur

public function userBookings(Request $request, $user_id)
{  // Valider la requête
    $request->validate([
        'start_index' => 'numeric|min:0',
        'end_index' => 'numeric|min:1',
        'page' => 'numeric|min:1',
    ]);

    $start_index = $request->input('start_index', 1);
    $end_index = $request->input('end_index', 2); // Par défaut, 10 éléments si end_index n'est pas spécifié
    $page = $request->input('page', 1);

    // Vérifier si l'utilisateur existe
    $user = User::find($user_id);

    if (!$user) {
        return response(["message" => "User not found with id: $user_id "], 404);
    }



    
    // Vérifier que l'utilisateur de la requête correspond à l'utilisateur des réservations
    if ($request->user_id != $user_id) {
        return response(['message' => 'Unauthorized'], 403);
    }


    // Récupérer les réservations de l'utilisateur (collection)
    $bookings = $user->bookings->makeHidden(['user_id']);

    // Paginer les résultats en utilisant start_index et end_index
    $paged_bookings = $bookings->slice(($page - 1) * ($end_index - $start_index + 1), $end_index - $start_index + 1);

    // Construire la réponse paginée
    $response_data = [
        'current_page' => $page,
        'limit' => $end_index - $start_index + 1,
        'total_pages' => ceil($bookings->count() / ($end_index - $start_index + 1)),
        'data' => $paged_bookings->values(), // Les données 
    ];

    return response($response_data, 200);
}




}












