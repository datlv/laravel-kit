<?php


namespace Datlv\Kit\Extensions\Html;


class ModelHtml
{
    /**
     * @var mixed|\Eloquent
     */
    protected $model;

    /**
     * ModelHtml constructor.
     *
     * @param mixed $model
     */
    public function __construct($model)
    {
        $this->model = $model;
    }
}
