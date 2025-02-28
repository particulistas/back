<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\Controller;
use App\Services\PropertiesService;
use Illuminate\Http\Request;


use App\Models\Property;
use App\Models\Media;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\Properties\PropertiesController;

class PropertiesController extends Controller
{
   /*  protected $propertiesService;

    public function __construct(PropertiesService $propertiesService)
    {
        $this->propertiesService = $propertiesService;
    }

    public function store(Request $request)
    {
        $property = $this->propertiesService->storePropertyData($request->all());

        return response()->json(['success' => true, 'property' => $property], 201);
    } */

    // Guardar los datos del primer paso
    public function storeFirstStep(Request $request)
    {
       /*  $request->validate([
            'type' => 'required|string',
            'transaction_type' => 'required|string',
            'sale_price' => 'nullable|numeric',
            'rental_price' => 'nullable|numeric',
            'm_built' => 'required|integer',
            'm_useful' => 'nullable|integer',
            'num_rooms' => 'required|integer',
            'num_bathrooms' => 'required|integer',
            'state' => 'nullable|string',
            'equipment' => 'nullable|string',
            'characteristics' => 'nullable|string',
        ]); */
        $data = $request->validate([
            'user_id' => 'required|string',
            'category_id' => 'required',
            'transaction' => 'required|string',
            'sale_price' => 'nullable|numeric',
            'rental_price' => 'nullable|numeric',
            'm_built' => 'required|integer',
            'm_usefull' => 'nullable|integer',
            'number_habs' => 'required|integer',
            'bathrooms' => 'required|integer',
            'state' => 'nullable|string',
            'equipment' => 'nullable|string',
            'characteristics' => 'nullable|json', // Validación para JSON
            'antique' => 'nullable|integer',
            'caracteristics_optionals' => 'nullable|string'

                  
              
        ]);

       /*  $property = Property::create($request->all());

        return response()->json([
            'message' => 'First step saved successfully',
            'property_id' => $property->id,
        ], 201); */

        $property = Property::create($data);

        return response()->json([
            'message' => 'First step saved successfully',
            'property_id' => $property->id,
        ], 201);

    }

    // Guardar las imágenes/videos
    public function storeMedia(Request $request)
    {
        $request->validate([
            'property_id' => 'required|exists:properties,id',
            'files' => 'required|array',
            'files.*' => 'file|mimes:jpeg,png,gif,mp4,avi,mov|max:10240', // 10MB máximo
        ]);

        $property = Property::findOrFail($request->property_id);

        foreach ($request->file('files') as $file) {
            $path = $file->store('public/media'); // Guarda el archivo en storage/app/public/media
            $media = new Media([
                'file_name' => $file->getClientOriginalName(),
                'file_path' => Storage::url($path),
                'file_type' => $file->getMimeType(),
            ]);
            $property->media()->save($media);
        }

        return response()->json([
            'message' => 'Media uploaded successfully',
        ], 201);
    }





}
