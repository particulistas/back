<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Models\User;

/**
 * @OA\OpenApi(
 *   @OA\Info(
 *     title="Nombre de tu API",
 *     version="1.0.0",
 *     description="Descripción de lo que hace tu API",
 *     @OA\Contact(
 *       email="tu-email@example.com"
 *     )
 *   ),
 *   @OA\Server(
 *     description="Servidor principal",
 *     url="http://localhost:8000/api"
 *   )
 * )
 * 
 * @OA\Schema(
 *   schema="User",
 *   type="object",
 *   description="User model",
 *   @OA\Property(property="id", type="integer", description="ID of the user"),
 *   @OA\Property(property="name", type="string", description="Name of the user"),
 *   @OA\Property(property="email", type="string", description="Email address of the user")
 * )
 */

class AuthController extends Controller
{


    /**
     * @OA\Post(
     *   path="/v1/register",
     *   summary="Register a new user",
     *   tags={"Authentication"},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       required={"name", "email", "password", "phone"},
     *       @OA\Property(property="name", type="string"),
     *       @OA\Property(property="email", type="string"),
     *       @OA\Property(property="password", type="string"),
     *       @OA\Property(property="phone", type="string")
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="User registered successfully"
     *   ),
     *   @OA\Response(
     *     response=422,
     *     description="Validation failed"
     *   )
     * )
     */
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

     /**
     * @OA\Post(
     *   path="/v1/login",
     *   summary="Authenticate user and return token",
     *   tags={"Authentication"},
     *   @OA\RequestBody(
     *     @OA\JsonContent(
     *       required={"email", "password"},
     *       @OA\Property(property="email", type="string"),
     *       @OA\Property(property="password", type="string")
     *     )
     *   ),
     *   @OA\Response(
     *     response=200,
     *     description="Authentication successful",
     *     @OA\JsonContent(
     *       @OA\Property(property="token", type="string"),
     *       @OA\Property(property="user", ref="#/components/schemas/User"),
     *       @OA\Property(property="role", type="string")
     *     )
     *   ),
     *   @OA\Response(
     *     response=401,
     *     description="Failed to authenticate"
     *   )
     * )
     */
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



    /**
     * @OA\Get(
     *   path="/v1/users",
     *   summary="Get list of users",
     *   tags={"Users"},
     *   @OA\Response(
     *     response=200,
     *     description="Successful operation",
     *     @OA\JsonContent(
     *       type="array",
     *       @OA\Items(ref="#/components/schemas/User")
     *     )
     *   )
     * )
     */
    public function getUsers(Request $request)
    {
        return response()->json($request->user());
    }

    /**
     * @OA\Delete(
     *   path="/v1/logout",
     *   summary="Log out a user",
     *   tags={"Authentication"},
     *   @OA\Response(
     *     response=200,
     *     description="Logged out successfully"
     *   )
     * )
     */
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
