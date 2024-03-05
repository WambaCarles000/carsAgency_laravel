<?php

namespace App\Http\Controllers;

use App\Models\Manufacturer;
use Illuminate\Http\Request;

class ManufacturerController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->input('limit', 2); // Nombre d'éléments par page, par défaut 10.

    $manufacturers = Manufacturer::paginate($limit);

    if ($manufacturers->isEmpty()) {
        return response(["message" => "No manufacturer found."], 404);
    }

    // Construire la réponse paginée
    $response_data = [
        'current_page' => $manufacturers->currentPage(),
        'limit' => $manufacturers->perPage(),
        'total_pages' => $manufacturers->lastPage(),
        'data' => $manufacturers->items(), // Les données réelles
    ];

    return response($response_data, 200);
    }

    public function store(Request $request)
    {
        $manufacturerData = $request->validate([
            "name" => ["required", "string"],
        ]);

        $newManufacturer = Manufacturer::create([
            "name" => $manufacturerData["name"],
            // "user_id" => $request->user()->id,
        ]);

        return response(['message' => 'New manufacturer stored.', 'data' => $newManufacturer], 201);
    }

    public function show($id)
    {
        $manufacturer = Manufacturer::find($id);

        if (!$manufacturer) {
            return response(["message" => "No manufacturer found with id: $id"], 404);
        }

        return response($manufacturer, 200);
    }

    public function update(Request $request, $id)
    {
        $manufacturerData = $request->validate([
            "name" => ["required", "string"],
        ]);

        $manufacturer = Manufacturer::find($id);

        if (!$manufacturer) {
            return response(["message" => "No manufacturer found with id: $id"], 404);
        }

        // // Vérification de l'autorisation
        // if ($request->user()->id != $manufacturer->user_id) {
        //     return response(["message" => "Prohibited"], 403);
        // }

        $manufacturer->update($manufacturerData);

        return response(['message' => 'Manufacturer updated.', 'data' => $manufacturer], 200);
    }

    public function destroy(Request $request, $id)
    {
        $manufacturer = Manufacturer::find($id);

        if (!$manufacturer) {
            return response(["message" => "No manufacturer found with id: $id"], 404);
        }

        // // Vérification de l'autorisation
        // if ($request->user()->id != $manufacturer->user_id) {
        //     return response(["message" => "Prohibited"], 403);
        // }

        $manufacturer->delete();

        return response(["message" => "Manufacturer deleted successfully"], 200);
    }
}
