<?php

namespace AppBundle\Model\Modflow;

abstract class AbstractModflowProcess
{
    /** @var  string */
    protected $model_id;

    /**
     * @return string
     */
    public function getModelId()
    {
        return $this->model_id;
    }

    /**
     * @param string $model_id
     * @return $this
     */
    public function setModelId($model_id)
    {
        $this->model_id = $model_id;
        return $this;
    }
}