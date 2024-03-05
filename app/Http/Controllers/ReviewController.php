<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\User;
use App\Http\Controllers\PaginationController;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    public function index(Request $request)
    {
       
        $limit = $request->input('limit', 2); // Nombre d'éléments par page, par défaut 2

    $reviews = Review::paginate($limit);

    if ($reviews->isEmpty()) {
        return response(["message" => "No review found."], 404);
    }

    // Construire la réponse paginée
    $response_data = [
        'current_page' => $reviews->currentPage(),
        'limit' => $reviews->perPage(),
        'total_pages' => $reviews->lastPage(),
        'data' => $reviews->items(), // Les données réelles
    ];

    return response($response_data, 200);
    }

    public function store(Request $request)
    {
        $reviewData = $request->validate([
            "comment" => ["required", "string"],
            "user_id" => ["required", "numeric"],
            "car_id" => ["required", "numeric"],
        ]);

        // Vérification de l'autorisation
        if ($request->user()->id != $reviewData["user_id"]) {
            return response(["message" => "Prohibited"], 403);
        }

        $newReview = Review::create([
            "comment" => $reviewData["comment"],
            "user_id" => $reviewData["user_id"],
            "car_id" => $reviewData["car_id"],
        ]);

        return response(['message' => 'New review stored.', 'data' => $newReview], 201);
    }

    public function show($id)
    {
        $review = Review::find($id);

        if (!$review) {
            return response(["message" => "No review found with id: $id"], 404);
        }

        return response($review, 200);
    }

    public function update(Request $request, $id)
    {
        $reviewData = $request->validate([
            "comment" => ["required", "string"],
            "user_id" => ["required", "numeric"],
            "car_id" => ["required", "numeric"],
        ]);

        $review = Review::find($id);

        if (!$review) {
            return response(["message" => "No review found with id: $id"], 404);
        }

        // Vérification de l'autorisation
        if ($request->user()->id != $review->user_id) {
            return response(["message" => "Prohibited"], 403);
        }

        $review->update($reviewData);

        return response(['message' => 'Review updated.', 'data' => $review], 200);
    }

    public function destroy(Request $request, $id)
    {
        $review = Review::find($id);

        if (!$review) {
            return response(["message" => "No review found with id: $id"], 404);
        }

        // Vérification de l'autorisation
        if ($request->user()->id != $review->user_id) {
            return response(["message" => "Prohibited"], 403);
        }

        $review->delete();

        return response(["message" => "Review deleted successfully"], 200);
    }




//user's reviews

    public function userReviews(Request $request,$user_id)
    {
       
    
                    // Valider la requête
            $request->validate([
                'start_index' => 'numeric|min:0',
                'end_index' => 'numeric|min:1',
                'page' => 'numeric|min:1',
                "user_id" => 'required|numeric',
            ]);


            // CHARGEMENT DES VALEURS PAR DEFAUT POUR LA PAGINATION
            $start_index = $request->input('start_index', 1);
            $end_index = $request->input('end_index', 2); // Par défaut, 2 éléments si end_index n'est pas spécifié
            $page = $request->input('page', 1);


        // Vérifier si l'utilisateur existe
        $user = User::find($user_id);
    
        if (!$user) {
            return response(['message' => "User not found with id: $user_id"], 404);
        }
    
        // Vérifier que l'utilisateur de la requête correspond à l'utilisateur des réservations
        if ($request->user_id != $user_id) {
            return response(['message' => 'Unauthorized'], 403);
        }
    
        // Récupérer les réservations de l'utilisateur
        $reviews = $user->reviews->makeHidden(['user_id']);

        // APPEL DE MON CONTROLEUR DE PAGINATION
    $paginationController = new PaginationController;

    $response_data = $paginationController->paginateResults($reviews, $page, $end_index - $start_index + 1);
     
    return response($response_data, 200);
    
        
    }



}
