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

use App\Models\BlockedUser;
use Illuminate\Http\JsonResponse;


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

     /**
     * Update the specified resource in storage.
     */
    //PUT
    public function update(Request $request, string $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }
        if ($request->input('name')) {
            $user->name = $request->input('name');
        }
        
        if ($user->email !=  $request->input('email')) {
            $emailExists  = User::where('email', $request->input('email'))->exists();
            if($emailExists ){
                return response()->json(['message' => 'El campo email ya ha sido tomado']);
            }
            $user->email = $request->input('email');
        }
        
        if ($request->input('mostrarCampos')) {
            if (!Hash::check($request->input('currentPassword'), $user->password)) {
                return response()->json(['message' => 'La Contraseña actual no es correcta']);
            }

            $user->password = Hash::make($request->input('newPassword'));
        }

        if($user->save()){

            $user->profile()->updateOrCreate(
                ['user_id' => $user->id],
                ['phone' => $request->input('code') .' '. $request->input('phone')],
            );

            return response()->json($user);
        }
        else{

            return response()->json([
                'error' => true,
                'message' => 'Error created user!'
            ], 201);

        }
    }

    public function getBlockedUsers(): JsonResponse
    {
        $blockedUsers = User::whereIn('id', function($query) {
            $query->select('blocked_user_id')
                  ->from('blocked_users')
                  ->where('user_id', Auth::id());
        })->get(['id', 'name', 'email']);

        return response()->json([
            'success' => true,
            'data' => $blockedUsers
        ]);
    }

    public function blockUser(User $user): JsonResponse
    {
        if ($user->id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot block yourself'
            ], 400);
        }

        BlockedUser::firstOrCreate([
            'user_id' => Auth::id(),
            'blocked_user_id' => $user->id
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User blocked successfully'
        ]);
    }

    public function unblockUser( $user): JsonResponse
    {
        BlockedUser::where('user_id', Auth::id())
            ->where('blocked_user_id', $user)
            ->delete();

        return response()->json([
            'success' => true,
            'message' => 'User unblocked successfully'
        ]);
    }


}
