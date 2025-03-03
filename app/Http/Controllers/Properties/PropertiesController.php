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
            'characteristics' => 'nullable|json', 
            'antique' => 'nullable|integer',
            'caracteristics_optionals' => 'nullable|string' 
        ]); */

        $property = Property::create($request->all());

        return response()->json([
            'message' => 'First step saved successfully',
            'property_id' => $property->id,
        ], 201);  

    }

    // Guardar los datos del segundo paso (dirección y descripción)
    public function storeSecondStep(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:properties,id',
            // 'is_last_floor' => 'nullable|boolean',
            // 'description' => 'required|string',
        ]);

        $property = Property::findOrFail($request->id);
        $property->update([
            'number_plants' => $request->number_plants,
            'address' => $request->address,
            'hide_address' => $request->hide_address, 
            'top_floor' => $request->top_floor, 
            'door' => $request->door, 
            'description' => $request->description,
        ]);

        
            // $vehicle->save();
              if ($request->hasFile('images')) {
                 $images = $request->file('images');
                 foreach ($images as $index => $image) {
                     if ($image) {
                         // Guarda la imagen en una carpeta en el servidorrr
                         $imageName = $image->store('public/uploads/pictures');
                         $url = Storage::url($imageName);
                         // Puedes guardar la información de la imagen en la base de datos 
                        /* $imageVehicle = ImageVehicle::create([
                             'vehicle_id' => $vehicle->id,
                             'name' => $url
                             //'name' =>  $tempName,
                         ]);*/
                         Media::create([
                            'properties_id' => $property->id,
                            'name' => $image->getClientOriginalName(),
                            'path' => $url,
                            'type' => $image->getClientMimeType(),
                            'postition' => $index,
                        ]); 
                     }
                 }
             } 


             // Guardar imágenes en la tabla media
  /*  if ($request->hasFile('images')) {
        foreach ($request->file('images') as $index => $image) {
          //  $filePath = $image->store('properties/' . $property->id, 'public');
            $filePath = $image->store('public/uploads/pictures');

             Media::create([
                'properties_id' => $property->id,
                'name' => $image->getClientOriginalName(),
                'path' => $filePath,
                'type' => $image->getClientMimeType(),
                'postition' => $index,
            ]); 
        }
    }/*
 
             return response()->json([
                'message' => 'Second step saved successfully',
            ], 200);
        

        /* return response()->json([
            'message' => 'Second step saved successfully',
        ], 200); */
    }

    // Guardar los datos del tercer paso (certificado energético)
    public function storeThirdStep(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:properties,id',
           // 'energy_certificate' => 'nullable|string', // Si tiene, en trámite, exento
           // 'energy_certificate_yes' => 'nullable|array', // Consumo (A, B, C, etc.)
        ]);

        $property = Property::findOrFail($request->id);
        $property->update([
            'energy_certificate' => $request->energy_certificate,
           // 'energy_certificate_yes' => json_encode($request->consumption), // Guardar como JSON
            'energy_certificate_yes' => $request->energy_certificate_yes, // Guardar como JSON
        ]);

        return response()->json([
            'message' => 'Third step saved successfully',
        ], 200);
    }

    // Guardar los datos del cuarto paso (datos de contacto)
    public function storeFourthStep(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:properties,id',
            /* 'publish_phone' => 'required|boolean', // Publicar teléfono
            'phone' => 'nullable|string', // Número de teléfono
            'phone_characteristics' => 'nullable|string', // Llamadas, WhatsApp, ambos */
        ]);

        $property = Property::findOrFail($request->id);
        $property->update([
            'publish_phone' => $request->publish_phone,
            'phone' => $request->phone,
            'phone_characteristics' => $request->phone_characteristics,
        ]);

        return response()->json([
            'message' => 'Fourth step saved successfully',
        ], 200);
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

    public function updateStatusProperties(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:properties,id',
        ]);

        $property = Property::findOrFail($request->id);
        $property->update([
            'status' => $request->status,
        ]);

        return response()->json([
            'message' => 'Sstatus saved successfully',
        ], 200);
    }







}
