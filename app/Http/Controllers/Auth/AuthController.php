<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Spatie\Permission\Models\Role;
use App\Mail\Auth\verifiedMailer;
use App\Mail\Auth\recoverPasswordMailer;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;
use App\Http\Controllers\Auth\AuthController;


class AuthController extends Controller
{

    public function store(Request $request)
    {

        $validatedData = $request->validate([
            'firstname' => 'required|string',
            'lastname' => 'string|nullable',
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
                'message' => __('auth.failed')
            ], 401);
        }

        Mail::to($request->email)->send(new verifiedMailer($user));

        return response()->json([
            'success' => true,
            'token' => $user_token,
            'user' => $user,
            'message' => __('auth.successCreate')
        ], 201);

        return response()->json(['message' => __('auth.successCreate')], 200);
    
        
    }

    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);  

        if (Auth::guard('web')->attempt(['email' => request('email'), 'password' => request('password')])) {
             // successfull authentication
            $user = Auth::guard('web')->user();
            $user_token['token'] = $user->createToken('appToken')->accessToken;
            $user2 = User::where('id', $user->id)->with('roles')->first();

            return response()->json([
                'success' => true,
                'token' => $user_token,
                'user' => $user,
                'role' => $user2->roles[0]->name, 
                'profile' => $user2->profile,
            ], 200); 
        } else {
            // failure to authenticate
            return response()->json([
                'success' => false,
                'message' => __('auth.failed'),
            ], 401); 
        } 

    } 


    public function getUsers(Request $request)
    {
        return response()->json($request->user()->load('profile'));
    }


    public function destroy(Request $request)
    {
        if (Auth::user()) {
            $request->user()->token()->revoke();

            return response()->json([
                'success' => true,
                'message' => __('auth.logout'),
            ], 200);
        }
    }

    public function verifiedMail(Request $request)
    {
        $user = User::where('remember_token', $request->token)->first();
        
        if ($user) {

            $user->remember_token = null;
            $user->email_verified_at = Carbon::now();
            $user->save();

            return response()->json([
                'success' => true,
                'message' => __('auth.emailVerified'),
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => __('auth.tokenFailed'),
        ], 400);
    }

    public function resendMailVerified(Request $request)
    {
        /* $validatedData = $request->validate([
            'email' => 'required|string|email',
        ]); */

        $randomCode = Str::random(32);
        
        $user = User::where('email', $request->email)->first();
        $user->remember_token = $randomCode;
        $user->save();
        
        Mail::to($request->email)->send(new verifiedMailer($user));

        return response()->json([
            'success' => true,
            'message' => __('auth.sendEmailSuccess'),
        ], 200);
    }

    public function recoveryPassword(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        
        if ($user) {
            //$randomCode = Str::random(32);
            $password = Str::random(8);

           // $user->remember_token = $randomCode;
            $user->password = $password;
            $user->save();

            Mail::to($request->email)->send(new recoverPasswordMailer($user,$password));

            return response()->json([
                'success' => true,
                'message' => __('auth.sendEmailSuccess'),
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => __('auth.sendEmailFailed'),
        ], 400);
    }

    public function newPassword(Request $request, $token)
    {
        $validatedData = $request->validate([
            'password' => 'required|string|confirmed'
        ]);

        $user = User::where('remember_token', $token)->first();

        if ($user) {
            $user->password = hash::make($request->password);
            $user->remember_token = null;
            $user->save();

            return response()->json([
                'success' => true,
                'message' => __('auth.passwordSuccess'),
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => __('auth.tokenFailed'),
        ], 400);
    }
}
