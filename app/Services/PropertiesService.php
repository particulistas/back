<?php

namespace App\Services;

use App\Repositories\PropertiesRepository;
use App\Repositories\MediasRepository;
use Illuminate\Support\Facades\Storage;

class PropertiesService
{
    protected $propertiesRepository;
    protected $mediasRepository;

    public function __construct(PropertiesRepository $propertiesRepository, MediasRepository $mediasRepository)
    {
        $this->propertiesRepository = $propertiesRepository;
        $this->mediasRepository = $mediasRepository;
    }

    
    public function storePropertyData(array $data)
    {
        //proceso los datos de la propiedad
        $processedData = $this->processData($data);

        //almaceno la propiedad
        $property = $this->propertiesRepository->saveProperty($processedData);
        
        // Procesar y almacenar las imágenes asociadas
        if (isset($data['images']) && is_array($data['images'])) {
            $this->processAndStoreMedia($data['images'], $property->id, "Properties");
        }
        return $property;
    }

    protected function processData(array $data)
    {
        $mappedData = [
            'user_id' => $data['user_id'] ?? null,
            'category_id' => $data['category_id'] ?? null,
            'transaction' => $data['transaction'] ?? 'sale',
            'sale_price' => $data['sale_price'] ?? null,
            'rental_price' => $data['rental_price'] ?? null,
            'bills' => $data['bills'] ?? null,
            'm_built' => $data['m_built'] ?? null,
            'm_usefull' => $data['m_usefull'] ?? null,
            'bathrooms' => $data['bathrooms'] ?? null,
            'number_plants' => $data['number_plants'] ?? null,
            'number_habs' => $data['number_habs'] ?? null,
            'distibutions' => $data['distibutions'] ?? null,
            'state' => $data['state'] ?? null,
            'equipment' => $data['equipment'] ?? null,
            'ubication' => $data['ubication'] ?? null,
            'characteristics' => $data['characteristics'] ?? null,
            'preferences' => $data['preferences'] ?? null,
            'cohabitation' => $data['cohabitation'] ?? null,
            'antique' => $data['antique'] ?? null,
            'address' => $data['address'] ?? null,
            'latitude' => $data['latitude'] ?? null,
            'longitude' => $data['longitude'] ?? null,
            'hide_address' => $data['hide_address'] ?? 0,
            'top_floor' => $data['top_floor'] ?? 0,
            'door' => $data['door'] ?? null,
            'description' => $data['description'] ?? null,
            'optionals' => $data['optionals'] ?? null,
            'energy_certificate' => $data['energy_certificate'] ?? "exempt",
            'energy_certificate_yes' => $data['energy_certificate_yes'] ?? null,
            'publish_phone' => $data['publish_phone'] ?? 0,
            'phone' => $data['phone'] ?? null,
            'phone_characteristics' => $data['phone_characteristics'] ?? "both",
            'status' => $data['status'] ?? "Draft",
            //'caracteristics_optionals' => $this->buildCaracteristicsOptionals($data)
        ];

        return $mappedData;
    }

    protected function buildCaracteristicsOptionals(array $data)
    {
        return json_encode([
            'field1' => $data['field1'] ?? null,
            'field2' => $data['field2'] ?? null,
            'field3' => $data['field3'] ?? null,
        ]);
    }

    protected function processAndStoreMedia(array $images, $propertyId, $object)
    {
        $storagePath = "properties/{$propertyId}/";

        foreach ($images as $image) {
            if (isset($image['file']) && $image['file']->isValid()) {
                // Mover el archivo a la carpeta de almacenamiento
                $filePath = $image['file']->store($storagePath, 'public');

                $extension = $image['file']->extension(); // 'jpg', 'png', etc.

                $mediaData = [
                    'properties_id' => $propertyId,
                    'name' => $image['name'] ?? pathinfo($filePath, PATHINFO_FILENAME),
                    'path' => $filePath,
                    'type' => $extension,
                    'object' => $object,
                    'position' => $image['position'] ?? 0,
                ];

                // Almacenar cada imagen en la base de datos
                $this->mediasRepository->saveMedia($mediaData);
            }
        }
    }
}
