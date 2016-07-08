<?php

namespace AppBundle\Process;

class InterpolationResult
{

    /** @var  string */
    protected $algorithm;

    /** @var  string */
    protected $data;

    public function __construct($algorithm, $data)
    {
        $this->algorithm = $algorithm;
        $this->data = $data;
    }

    /**
     * @return string
     */
    public function getAlgorithm()
    {
        return $this->algorithm;
    }

    /**
     * @param string $algorithm
     * @return $this
     */
    public function setAlgorithm($algorithm)
    {
        $this->algorithm = $algorithm;
        return $this;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param string $data
     * @return $this
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }


}