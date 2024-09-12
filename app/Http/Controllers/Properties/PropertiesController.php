<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PropertiesController extends Controller
{
    public function store(Request $request)
    {
        $user = $request->user();


        return response()->json([
            'success' => true,
            'message' => __('content.storeProperty'),
        ], 200);
        
    }
}
