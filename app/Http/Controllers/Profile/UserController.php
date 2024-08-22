<?php

namespace App\Http\Controllers\Profile;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Profile;

class UserController extends Controller
{

    /**
    * @OA\Patch(
    *   path="/v1/update-profile",
    *   summary="Update user profile",
    *   tags={"Users"},
    *   @OA\RequestBody(
    *     @OA\JsonContent(
    *       @OA\Property(property="firstname", type="string"),
    *       @OA\Property(property="lastname", type="string"),
    *       @OA\Property(property="phone", type="string"),
    *       @OA\Property(property="gender", type="string")
    *     )
    *   ),
    *   @OA\Response(
    *     response=200,
    *     description="Profile updated successfully",
    *     @OA\JsonContent(
    *       @OA\Property(property="success", type="boolean"),
    *       @OA\Property(property="message", type="string")
    *     )
    *   ),
    *   @OA\Response(
    *     response=400,
    *     description="Invalid input",
    *     @OA\JsonContent(
    *       @OA\Property(property="success", type="boolean"),
    *       @OA\Property(property="message", type="string")
    *     )
    *   )
    * )
    */
    
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
