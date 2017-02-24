<?php

namespace Inowas\ModflowBundle\Model;

class GridSize implements \JsonSerializable
{
    /** @var int  */
    protected $nX;


    /** @var int  */
    protected $nY;

    /**
     * @param array $gridSizeArray
     * @return GridSize
     */
    public static function fromArray(array $gridSizeArray): GridSize
    {
        $self = new self();
        $self->nX = $gridSizeArray['n_x'];
        $self->nY = $gridSizeArray['n_y'];
        return $self;
    }

    /**
     * GridSize constructor.
     * @param int $nX
     * @param int $nY
     */
    public function __construct($nX = 0, $nY = 0)
    {
        $this->nX = $nX;
        $this->nY = $nY;
    }

    /**
     * @return int
     */
    public function getNX()
    {
        return $this->nX;
    }

    /**
     * @return int
     */
    public function getNumberOfColumns(){
        return $this->getNX();
    }

    /**
     * @param int $nX
     * @return GridSize
     */
    public function setNX($nX)
    {
        $this->nX = $nX;
        return $this;
    }

    /**
     * @return int
     */
    public function getNY()
    {
        return $this->nY;
    }

    /**
     * @return int
     */
    public function getNumberOfRows(){
        return $this->getNY();
    }

    /**
     * @param int $nY
     * @return GridSize
     */
    public function setNY($nY)
    {
        $this->nY = $nY;
        return $this;
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return array(
            'n_x' => $this->getNX(),
            'n_y' => $this->getNY()
        );
    }

    public function toArray(): array
    {
        return array(
            'n_x' => $this->getNX(),
            'n_y' => $this->getNY()
        );
    }
}
