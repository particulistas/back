<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use App\Models\Profile;
use App\Models\User;


use Illuminate\Validation\ValidationException;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use App\Mail\Auth\verifiedMailer;
use App\Mail\Auth\recoverPasswordMailer;
use Illuminate\Support\Str;


use Carbon\Carbon;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Profile\UserController;


class UserController extends Controller
{

    /* public function updateProfile(Request $request)
    {
        $user = $request->user();

        if ($request->firstname && $request->lastname) {
            $user->name = $request->firstname." ".$request->lastname;
        }

        $user->save();

        $profile = Profile::where('user_id', $user->id)->first();

        if ($request->firstname) {
            $profile->firstname = $request->firstname;
        }

        if ($request->lastname) {
            $profile->lastname = $request->lastname;
        }

        if ($request->phone) {
            $profile->phone = $request->phone;
        }

        if ($request->gender) {
            $profile->gender = $request->gender;
        }

        $profile->save();

        return response()->json([
            'success' => true,
            'message' => __('user.updateProfile'),
        ], 200);
        
    } */

    public function show(Request $request)
    {
        $user = User::with('profile')->find($request->id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        return response()->json($user);
    }
}
