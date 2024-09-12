<?php
namespace App\Repositories;

use App\Models\Property;

class PropertiesRepository
{
    protected $model;

    public function __construct(Property $model)
    {
        $this->model = $model;
    }

    public function saveProperty(array $data)
    {
        return $this->model->create($data);
    }
}
