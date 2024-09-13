<?php

namespace App\Services;

use App\Repositories\PropertiesRepository;

class PropertiesService
{
    protected $propertiesRepository;

    public function __construct(PropertiesRepository $propertiesRepository)
    {
        $this->propertiesRepository = $propertiesRepository;
    }

    public function storePropertyData(array $data)
    {
        $processedData = $this->processData($data);

        return $this->propertiesRepository->saveProperty($processedData);
    }

    protected function processData(array $data)
    {
        return $data;
    }
}
