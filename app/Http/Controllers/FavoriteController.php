<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\Property;
use App\Models\User;
use App\Models\Favorite;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
   /*  public function __construct()
    {
        $this->middleware('auth:api'); // Cambiado a auth:api
    } */


     // app/Http/Controllers/FavoriteController.php
    public function userFavorites($user_id)
    {
        $user = User::where('id', $user_id)->first();
       // return $user->favoriteProperties()->paginate(10);

        return User::findOrFail($user_id)
              ->favoriteProperties()
              ->with([
                  'media' => fn($q) => $q->orderBy('postition'),
                  'category',
                  'user.profile' // Si necesitas datos del perfil del usuario
              ])
              ->paginate(10);
    } 

   /*  public function userFavorites($id)
    {
        $favorites = Favorite::with('vehicle.yearVehicle', 'vehicle.brand','vehicle.modelVehicle','vehicle.imagesVehicle','vehicle.color', 'user')
        ->where('user_id','=', $id)
        //->take(6)
        //->get();
        ->paginate(20);

        return response()->json($favorites);
    } */
 /*    public function index()
    {
        try {
            $user = Auth::guard('api')->user();
            
            if (!$user) {
                throw new \Exception('Usuario no autenticado');
            }

            return $user->favoriteProperties()->paginate(10);
            
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 401);
        }
    } */

    public function store($id)
    {
        auth()->user()->favorites()->firstOrCreate(['property_id' => $id]);
        return response()->json(['message' => 'Added to favorites']);
    }

    public function destroy($id)
    {
        auth()->user()->favorites()->where('property_id', $id)->delete();
        return response()->json(['message' => 'Removed from favorites']);
    }

    public function checkFavorite($id)
    {
        $user = auth()->user();
       // $user = User::where('id', 53)->first();
        
        if (!$user) {
            return response()->json([
                'error' => 'Authentication required'
            ], 401);
        }

        $isFavorite = $user->favorites()
                         ->where('property_id', $id)
                         ->exists();

        return response()->json([
            'isFavorite' => $isFavorite,
            'property_id' => $id,
            'user_id' => $user->id
        ]);
    }
}
