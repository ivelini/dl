<?php


namespace App\Repositories;



abstract class CoreRepository
{

    abstract public function getModelClass();

    protected $modelClass;

    public function __construct()
    {
        $this->modelClass = $this->getModelClass();
    }

    public function startCondition()
    {
        return new $this->modelClass();
    }

    public function getModel($id)
    {
        return $this->startCondition()->where('id', $id)->first();
    }

}
