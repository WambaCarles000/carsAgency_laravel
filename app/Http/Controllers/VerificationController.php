<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\VerifiesEmails;

class VerificationController extends Controller
{
    use VerifiesEmails;


    public function __construct()
    {
        // $this->middleware('auth:sanctum')->only('verify');
    }

    public function verify(Request $request)
    {
        // Récupérer l'utilisateur et les paramètres de l'URL
        $user = $this->getUser($request->id);

        // Vérifier si l'utilisateur existe et la signature de l'URL est valide
        if ($user && hash_equals((string) $request->hash, sha1($user->getEmailForVerification()))) {
            // Marquer l'e-mail comme vérifié
            $user->markEmailAsVerified();

            return response()->json(['message' => 'Email verified successfully.'], 200);
        }

        return response()->json(['message' => 'Invalid verification link.'], 401);
    }
}
