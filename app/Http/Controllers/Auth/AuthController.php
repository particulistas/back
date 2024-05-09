<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;

class AuthController extends Controller
{

    //Valida y registra nuevo usuario
    public function store(Request $request)
    {

        try {
        $validatedData = $request->validate([
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string',
            'phone' => 'required|string'
        ]);
        
        // Lógica de registro aquí...

        return response()->json(['message' => 'User registered successfully!'], 200);
    } catch (ValidationException $e) {
        return response()->json($e->errors(), 422);
    }
    /*
        $user = $user_token = null;
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'password' => hash::make($request->password)
        ]);
        
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {

            $user = User::find(Auth::user()->id);
            $user_token['token'] = $user->createToken('appToken')->accessToken;
        }

        return response()->json([
            'success' => true,
            'token' => $user_token,
            'user' => $user,
            'message' => 'Successfully created user!'
        ], 201);*/
    }
    //POST
    //Autentica un usuario
    public function login()
    {
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
             // successfull authentication
            $user = User::find(Auth::user()->id);
            $user_token['token'] = $user->createToken('appToken')->accessToken;
            $user2 = User::where('id', $user->id)->with('roles')->first();

            return response()->json([
                'success' => true,
                'token' => $user_token,
                'user' => $user,
                'role' => $user2->roles[0]->name,
            ], 200);
         } else {
            // failure to authenticate
            return response()->json([
                'success' => false,
                'message' => 'Failed to authenticate.',
            ], 401); 
        }
    }
    //GET
    //Muestra los datos de un usuario
    public function show(Request $request)
    {
        return response()->json($request->user());
    }
    //DELETE
    //Elimina la información del usuario
    public function destroy(Request $request)
    {
        if (Auth::user()) {
            $request->user()->token()->revoke();

            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully',
            ], 200);
        }
    }
}
