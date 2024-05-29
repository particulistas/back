<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Profile;

class UserController extends Controller
{
    public function updateProfile(Request $request)
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
        
    }
}
