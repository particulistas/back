<?php

namespace App\Http\Controllers\Properties;

use App\Http\Controllers\Controller;
use App\Services\PropertiesService;
use Illuminate\Http\Request;


use App\Models\Property;
use App\Models\Media;
use App\Models\Category;
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


    public function updateFirstStep(Request $request)
    {
        // Validar los datos de entrada
       /*  $validated = $request->validate([
            'category_id' => 'required|exists:categories,id',
            'transaction' => 'required|in:sale,rental,both',
            'sale_price' => 'nullable|numeric|min:0',
            'rental_price' => 'nullable|numeric|min:0',
            'm_built' => 'required|numeric|min:0',
            'm_usefull' => 'nullable|numeric|min:0',
            'number_habs' => 'required|integer|min:0',
            'bathrooms' => 'required|integer|min:0',
            'state' => 'nullable|string',
            'equipment' => 'nullable|string',
            'characteristics' => 'nullable|json',
            'antique' => 'nullable|string',
            'caracteristics_optionals' => 'nullable|string'
        ]); */

        try {
            // Buscar la propiedad a actualizar
            $property = Property::findOrFail($request->id);

            // Actualizar los campos
            $property->update([
                'category_id' => $request->category_id,
                'transaction' => $request->transaction,
                'sale_price' => $request->sale_price,
                'rental_price' => $request->rental_price,
                'm_built' => $request->m_built,
                'm_usefull' => $request->m_usefull,
                'number_habs' => $request->number_habs,
                'bathrooms' => $request->bathrooms,
                'state' => $request->state,
                'equipment' => $request->equipment,
                'characteristics' => $request->characteristics,
                'antique' => $request->antique,
                'caracteristics_optionals' => $request->caracteristics_optionals
            ]);

          /*   return response()->json([
                'success' => true,
                'property_id' => $property->id,
                'message' => 'Primer paso actualizado correctamente'
            ]); */

            return response()->json([
                'success' => true,
                'message' => 'First step saved successfully',
                'property_id' => $property->id,
            ], 201);  

          

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al actualizar la propiedad: ' . $e->getMessage()
            ], 500);
        }
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
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
        ]);


        // Solo procesar imágenes si hubo cambios
    if ($request->imagesChanged) {
     
        // Eliminar solo imágenes marcadas para borrar
     /*    if ($request->has('images_to_delete')) {
            
            $images2 = $request->file('images_to_delete');
            foreach ($images2 as $index => $image2) {
                if ($image2) {
                    Media::where('properties_id', $property->id)
                        ->whereIn('id', $image2->id)
                        ->delete();
                }
            }
        } */

        // Verificar que el arreglo no esté vacío y sea un arreglo
   /*      if (!empty($request->imagesToDelete) ) {
            // Recorrer cada ID en el arreglo
            Media::create([
                'properties_id' => $property->id,
                'name' => 'prueba2',
                'path' => $request->imagesToDelete,
                'type' => 'prueba2',
                'postition' => 0,
            ]); 
            foreach ($request->imagesToDelete as $imageId) {
                // Eliminar el registro específico
                Media::where('properties_id', $property->id)
                    ->where('id', $imageId)  // Usamos where en lugar de whereIn para un solo ID
                    ->delete();
            }
        } */

        if (!empty($request->imagesToDelete)) {
            // Convertir la cadena en un array
            $imageIds = explode(',', $request->imagesToDelete);
            
            // Opcional: limpiar los valores (eliminar espacios en blanco)
            $imageIds = array_map('trim', $imageIds);
            
            // Opcional: filtrar solo valores numéricos
            $imageIds = array_filter($imageIds, 'is_numeric');
            
            // Eliminar cada registro
            foreach ($imageIds as $imageId) {
                Media::where('properties_id', $property->id)
                     ->where('id', $imageId)
                     ->delete();
            }
        }

        // Guardar nuevas imágenes
        if ($request->hasFile('images')) {
            $images = $request->file('images');
            foreach ($images as $index => $image) {
                if ($image) {
                    // Guarda la imagen en una carpeta en el servidorrr
                    $imageName = $image->store('public/uploads/pictures');
                    $url = Storage::url($imageName);
                    // Puedes guardar la información de la imagen en la base de datos 
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
    }

       // Media::where('properties_id', $property->id)->delete();

       /*  if ($request->hasFile('images')) {
                 $images = $request->file('images');
            foreach ($images as $index => $image) {
                if ($image) {
                    // Guarda la imagen en una carpeta en el servidorrr
                    $imageName = $image->store('public/uploads/pictures');
                    $url = Storage::url($imageName);
                    // Puedes guardar la información de la imagen en la base de datos 
                    Media::create([
                        'properties_id' => $property->id,
                        'name' => $image->getClientOriginalName(),
                        'path' => $url,
                        'type' => $image->getClientMimeType(),
                        'postition' => $index,
                    ]); 
                }
            }
        }  */

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

        if ($request->imagesChanged) {

            if (!empty($request->imagesToDelete)) {
                // Convertir la cadena en un array
                $imageIds = explode(',', $request->imagesToDelete);
                
                // Opcional: limpiar los valores (eliminar espacios en blanco)
                $imageIds = array_map('trim', $imageIds);
                
                // Opcional: filtrar solo valores numéricos
                $imageIds = array_filter($imageIds, 'is_numeric');
                
                // Eliminar cada registro
                foreach ($imageIds as $imageId) {
                    Media::where('properties_id', $property->id)
                         ->where('id', $imageId)
                         ->delete();
                }
            }

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
                        'object' => 'plano',
                        'type' => $image->getClientMimeType(),
                        'postition' => $index,
                    ]); 
                    }
                }
            } 
        }


       /* if ($request->hasFile('imagesPlano')) {
            $imagesPlano = $request->file('imagesPlano');
            foreach ($imagesPlano as $indexPlano => $imagePLano) {
                if ($imagePlano) {
                    // Guarda la imagen en una carpeta en el servidorrr
                    $imageNamePlano = $imagePlano->store('public/uploads/pictures');
                    $urlPlano = Storage::url($imageNamePlano);
                    // Puedes guardar la información de la imagen en la base de datos 
                
                    Media::create([
                       'properties_id' => $property->id,
                       'name' => $imagePlano->getClientOriginalName(),
                       'path' => $urlPlano,
                       'object' => 'plano',
                       'type' => $imagePlano->getClientMimeType(),
                       'postition' => $indexPlano,
                   ]); 
                }
            }
        } */

       /*  return response()->json([
            'message' => 'Third step saved successfully',
        ], 200); */
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

    public function show($id)
    {
        $property = Property::with([ 'media', 'category', 'user'])->findOrFail($id);
        
        return response()->json($property);
    }

    public function showByUserId($user_id)
    {
        $properties = Property::with(['media', 'category', 'user'])
                            ->where('user_id', $user_id)
                            ->paginate(10); // 10 items por página
        
        return response()->json($properties);
    }

    public function showAll()
    {
       /*  $properties = Property::with(['media', 'category', 'user'])
                            ->paginate(10); // 10 items por página */

        $properties = Property::with(['media', 'category', 'user'])->get(); // Obtiene todos los registros
       // $properties = Property::all(); 
        
        return response()->json($properties);
    }

  

  /*  public function show($id)
    {
       // $user = User::find($id);
        $vehicle = Vehicle::where('id', $id)->with('brand', 'modelVehicle','imagesVehicle','color','yearVehicle')->first();
       

        if (!$vehicle) {
            return response()->json(['message' => 'Vehiculo no encontrado'], 404);
        }

        return response()->json($vehicle);

    }*/

}
