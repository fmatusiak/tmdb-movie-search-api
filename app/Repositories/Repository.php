<?php

namespace App\Repositories;

class Repository
{
    protected $model;

    public function __construct($model)
    {
        $this->model = $model;
    }

    public function find(int $id)
    {
        return $this->model::find($id);
    }

    public function firstOrCreate(array $data)
    {
        return $this->model::firstOrCreate($data);
    }

    public function whereFirst(array $conditions)
    {
        return $this->model::where($conditions)->first();
    }
}
