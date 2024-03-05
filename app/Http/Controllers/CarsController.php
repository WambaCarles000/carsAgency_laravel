<?php

namespace App\Http\Controllers;

use App\Models\Cars;
use App\Models\User;
use App\Http\Controllers\PaginationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class CarsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {


        $limit = $request->input('limit', 2); // Nombre d'éléments par page, par défaut 10.

    $cars = Cars::paginate($limit);

    if ($cars->isEmpty()) {
        return response(["message" => "No car found."], 404);
    }

    // Construire la réponse paginée
    $response_data = [
        'current_page' => $cars->currentPage(),
        'limit' => $cars->perPage(),
        'total_pages' => $cars->lastPage(),
        'data' => $cars->items(), // Les données réelles
    ];

    return response($response_data, 200);
      

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        
        $carData  = $request->validate([
              
            "model" => ["required","string"],
            "description" => ["required","string"],
            "price" => ["required","numeric"],
            "user_id" => ["required","numeric"],
            "manufacturer_id" => ["required","numeric"],
            "category_id" => ["required","numeric"],
       ]);


      

           $newCar = Cars::create([

            "model" => $carData["model"],
            "description" => $carData["description"],
            "price" => $carData["price"] ,
            "user_id" =>$carData["user_id"],
            "manufacturer_id"=>$carData["manufacturer_id"],
            "category_id" =>$carData["category_id"],
           ])   ;           

           return response(['message '=>'New car stored ',$newCar], 201);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Cars  $cars
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // $car = Cars::find($id)->first();
     
        $car = DB::table("cars")
        ->join("users","cars.user_id","=","users.id")
        ->select("cars.*","users.name","users.email")
        ->where("cars.id" ,"=",$id)
        ->get()
        ->first();

        return $car;
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Cars  $cars
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

            //    ENTREE DE NOUVELLES DONNEES
        $carData  = $request->validate([
              
            "model" => ["required","string"],
            "description" => ["required","string"],
            "price" => ["required","numeric"],
            "user_id" => ["required","numeric"],
       ]);

    //    VERIFICATION

        $car = Cars::find($id);


        if(!$car)
       return response(["message"=> "no car found with id : $id"],404);

    //MIDDLE WARE D'AUTHENTIFICATION

    if($carData["user_id"] != $car->user_id)
    return response(["message"=> "prohibited"],403);

             

         $carUpdated = $car->update($carData)   ;           

           return response(['message '=>'Car updated ',"updateState"=>$carUpdated], 201);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Cars  $cars
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        // RÉCUPÉRATION DE L'IDENTIFIANT DE L'UTILISATEUR
        $carData = $request->validate([
            "user_id" => ["required", "numeric"],
        ]);
    
        // RECHERCHE DE LA VOITURE CORRESPONDANTE
        $car = Cars::find($id);
    
        if (!$car) {
            return response(["message" => "No car found with id: $id"], 404);
        }
    
        // MIDDLEWARE D'AUTHENTIFICATION
        if ($carData["user_id"] != $car->user_id) {
            return response(["message" => "Prohibited"], 403);
        }
    
        // SUPPRESSION DE LA VOITURE
        $deleteState = $car->delete();
    
        return response(["message" => "Car deleted successfully", "deleteState" => $deleteState], 200);
    }





    
public function userCars(Request $request,$user_id)
{
   



 // Valider la requête
 $request->validate([
    'start_index' => 'numeric|min:0',
    'end_index' => 'numeric|min:1',
    'page' => 'numeric|min:1',
    "user_id" => ["required", "numeric"],
]);

$start_index = $request->input('start_index', 1);
$end_index = $request->input('end_index', 1); // Par défaut, 10 éléments si end_index n'est pas spécifié
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
    $cars = $user->cars->makeHidden(['user_id']);

    // return response(['cars' => $cars], 200);


// APPEL DE MON CONTROLEUR DE PAGINATION
    $paginationController = new PaginationController;

    $response_data = $paginationController->paginateResults($cars, $page, $end_index - $start_index + 1);
     
    return response($response_data, 200);

}
    
}
