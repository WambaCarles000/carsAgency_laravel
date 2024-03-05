<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RegistrationController;
use App\Http\Controllers\VerificationController;
use App\Http\Controllers\CarsController;
use App\Http\Controllers\ManufacturerController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\CategoryController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/



//USER

Route::post("/user/signup",[RegistrationController::class,"signup"]);
Route::post("/user/login",[RegistrationController::class,"login"]);
// Route::get('/user/confirm/{id}/{hash}', [VerificationController::class, "verify"])->name('verification.verify');

//email notice
Route::get('/email/verify', function () {
    return view('auth.verify-email');
})->middleware('auth')->name('verification.notice');


//  email handler
Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
 
    return redirect('/home');
})->middleware(['auth', 'signed'])->name('verification.verify');


//CARS
Route::get("/cars",[CarsController::class,"index"]);
Route::get("/cars/{id}",[CarsController::class,"show"]);
Route::get("/cars/user/{user_id}",[CarsController::class,"userCars"]);


// BOOKING


Route::get("/booking",[ BookingController::class,"index"]);
Route::get("/booking/{id}",[BookingController::class,"show"]);
Route::get("/booking/user/{user_id}",[BookingController::class,"userBookings"]);


// CATEGORY

Route::get("/category",[CategoryController::class,"index"]);
Route::get("/category/{id}",[CategoryController::class,"show"]);


// MANUFACTURER


Route::get("/manufacturer",[ManufacturerController::class,"index"]);
Route::get("/manufacturer/{id}",[ManufacturerController::class,"show"]);



//REVIEW

Route::get("/review",[ReviewController::class,"index"]);
Route::get("/review/{id}",[ReviewController::class,"show"]);
Route::get("/review/user/{user_id}",[ReviewController::class,"userReviews"]);




// SECURISATION DES ROUTES 
Route::group(["middleware" => ['auth:sanctum']], function () {
    Route::put("/cars/{id}", [CarsController::class, "update"]);
    Route::delete("/cars/{id}", [CarsController::class, "destroy"]);
    Route::post("/cars/store", [CarsController::class, "store"]);


    Route::put("/booking/{id}", [BookingController::class, "update"]);
    Route::delete("/booking/{id}", [BookingController::class, "destroy"]);
    Route::post("/booking/store", [BookingController::class, "store"]);

    Route::put("/category/{id}", [CategoryController::class, "update"]);
    Route::delete("/category/{id}", [CategoryController::class, "destroy"]);
    Route::post("/category/store", [CategoryController::class, "store"]);

    Route::put("/manufacturer/{id}", [ManufacturerController::class, "update"]);
    Route::delete("/manufacturer/{id}", [ManufacturerController::class, "destroy"]);
    Route::post("/manufacturer/store", [ManufacturerController::class, "store"]);

    Route::put("/review/{id}", [ReviewController::class, "update"]);
    Route::delete("/review/{id}", [ReviewController::class, "destroy"]);
    Route::post("/review/store", [ReviewController::class, "store"]);


  
        

});


