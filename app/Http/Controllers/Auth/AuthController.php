<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use App\Mail\Auth\verifiedMailer;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;


/**
 * @OA\OpenApi(
 *   @OA\Info(
 *     title="Endpoint PArticulistas",
 *     version="1.0.0",
 *     description="Endpoint donde se desarrolla todo el back de Particulistas",
 *     @OA\Contact(
 *       email="info@particulistas.com"
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
     *   path="/api/v1/register",
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

        $validatedData = $request->validate([
            'firstname' => 'required|string',
            'lastname' => 'required|string',
            'phone' => 'required|string',
            'email' => 'required|string|email|unique:users',
            'password' => 'required|string|confirmed'
        ]);
        
        $user = $user_token = null;
        $randomCode = Str::random(32);

        $user = User::create([
            'name' => $request->firstname." ".$request->lastname,
            'email' => $request->email,
            'remember_token' => $randomCode,
            'password' => hash::make($request->password)
        ]);

        $user->assignRole('client');
        
        $user->profile()->updateOrCreate([
            'firstname'  => $request->firstname,
            'lastname'   => $request->lastname,
            'phone'     => $request->phone,
            'created_at' => Carbon::now(),
            'updated_at' => Carbon::now(),
        ]);

        if (Auth::guard('web')->attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::guard('web')->user();
            $user_token['token'] = $user->createToken('appToken')->accessToken;
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        Mail::to($request->email)->send(new verifiedMailer($user));

        return response()->json([
            'success' => true,
            'token' => $user_token,
            'user' => $user,
            'message' => 'Successfully created user!'
        ], 201);

        return response()->json(['message' => 'User registered successfully!'], 200);
    
        
    }

     /**
     * @OA\Post(
     *   path="/api/v1/login",
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
    public function login(Request $request)
    {

        $validatedData = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        if (Auth::guard('web')->attempt(['email' => $request->email, 'password' => $request->password])) {
             // successfull authentication
            $user = Auth::guard('web')->user();
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
     *   path="/api/v1/users",
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
        return response()->json($request->user()->load('profile'));
    }

    /**
     * @OA\Delete(
     *   path="/api/v1/logout",
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

    /**
    * @OA\Post(
    *   path="/api/v1/verified/{token}",
    *   summary="Verify user email",
     *   tags={"Authentication"},
    *   @OA\Parameter(
    *     name="token",
    *     in="path",
    *     required=true,
    *     @OA\Schema(type="string"),
    *     description="Verification token"
    *   ),
    *   @OA\Response(
    *     response=200,
    *     description="Correo verificado exitosamente",
    *     @OA\JsonContent(
    *       @OA\Property(property="success", type="boolean"),
    *       @OA\Property(property="message", type="string")
    *     )
    *   ),
    *   @OA\Response(
    *     response=400,
    *     description="Error al verificar, token no válido",
    *     @OA\JsonContent(
    *       @OA\Property(property="success", type="boolean"),
    *       @OA\Property(property="message", type="string")
    *     )
    *   )
    * )
    */
    
    public function verifiedMail($token)
    {
        $user = User::where('remember_token', $token)->first();
        
        if ($user) {

            $user->remember_token = null;
            $user->email_verified_at = Carbon::now();
            $user->save();

            return response()->json([
                'success' => true,
                'message' => 'Correo verificado exitosamente',
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Error al verificar, token no válido',
        ], 400);
    }
}
