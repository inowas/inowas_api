<?php

namespace AppBundle\Model;

interface PropertyValueInterface
{
    /**
     * @return mixed
     */
    public function getDateBegin();

    /**
     * @return mixed
     */
    public function getDateEnd();

    /**
     * @return mixed
     */
    public function getNumberOfValues();

    /**
     * @return array TimeValue
     */
    public function getTimeValues();
}