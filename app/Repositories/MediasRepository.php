<?php
namespace App\Repositories;

use App\Models\Media;

class MediasRepository
{
    protected $model;

    public function __construct(Media $model)
    {
        $this->model = $model;
    }

    public function saveMedia(array $data)
    {
        return $this->model->create($data);
    }
}
