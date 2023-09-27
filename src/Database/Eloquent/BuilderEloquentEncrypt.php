<?php

namespace Gestazion\AESEncrypt\Database\Eloquent;

use Illuminate\Database\Eloquent\Builder;
use Gestazion\AESEncrypt\Database\Query\BuilderEncrypt as QueryBuilder;
use Illuminate\Database\Eloquent\Model;

class BuilderEloquentEncrypt extends Builder
{
    /**
     * Create a new Eloquent query builder instance.
     *
     * @param \Gestazion\AESEncrypt\Database\Query\BuilderEncrypt  $query
     * @return void
     */
    public function __construct(QueryBuilder $query)
    {
        $this->query = $query;
    }

    /**
     * Set a model instance for the model being queried.
     *
     * @param  \Illuminate\Database\Eloquent\Model  $model
     * @return $this
     */
    public function setModel(Model $model)
    {
        $this->model = $model;

        $this->query->from($model->getTable());

        $this->query->setFillableEncrypt($model->getfillableEncrypt());

        return $this;
    }
}
