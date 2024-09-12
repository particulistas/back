<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\Controller;
use App\Services\PropertiesService;
use Illuminate\Http\Request;

class PropertiesController extends Controller
{
    protected $propertiesService;

    public function __construct(PropertiesService $propertiesService)
    {
        $this->propertiesService = $propertiesService;
    }

    public function store(Request $request)
    {
        $property = $this->propertiesService->storePropertyData($request);

        return response()->json(['success' => true, 'property' => $property], 201);
    }
}
