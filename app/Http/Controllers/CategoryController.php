<?php 

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index(Request $request)
    {
        $limit = $request->input('limit', 2); // Nombre d'éléments par page, par défaut 10.

    $categories = Category::paginate($limit);

    if ($categories->isEmpty()) {
        return response(["message" => "No category found."], 404);
    }

    // Construire la réponse paginée
    $response_data = [
        'current_page' => $categories->currentPage(),
        'limit' => $categories->perPage(),
        'total_pages' => $categories->lastPage(),
        'data' => $categories->items(), // Les données réelles
    ];

    return response($response_data, 200);
      
    }

    public function store(Request $request)
    {
        $categoryData = $request->validate([
            "name" => ["required", "string"],
        ]);

        // Vérification de l'autorisation
        // if ($request->user()->id != $categoryData["user_id"]) {
        //     return response(["message" => "Prohibited"], 403);
        // }

        $newCategory = Category::create([
            "name" => $categoryData["name"],
        ]);

        return response(['message' => 'New category stored.', 'data' => $newCategory], 201);
    }

    public function show($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response(["message" => "No category found with id: $id"], 404);
        }

        return response($category, 200);
    }

    public function update(Request $request, $id)
    {
        $categoryData = $request->validate([
            "name" => ["required", "string"],
        ]);

        $category = Category::find($id);

        if (!$category) {
            return response(["message" => "No category found with id: $id"], 404);
        }

        // // Vérification de l'autorisation
        // if ($request->user()->id != $category->user_id) {
        //     return response(["message" => "Prohibited"], 403);
        // }

        $category->update($categoryData);

        return response(['message' => 'Category updated.', 'data' => $category], 200);
    }

    public function destroy(Request $request, $id)
    {
        $category = Category::find($id);

        if (!$category) {
            return response(["message" => "No category found with id: $id"], 404);
        }

        // // Vérification de l'autorisation
        // if ($request->user()->id != $category->user_id) {
        //     return response(["message" => "Prohibited"], 403);
        // }

        $category->delete();

        return response(["message" => "Category deleted successfully"], 200);
    }
}
