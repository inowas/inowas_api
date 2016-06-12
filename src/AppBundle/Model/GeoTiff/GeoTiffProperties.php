<?php

namespace AppBundle\Model\GeoTiff;

use AppBundle\Entity\Raster;
use AppBundle\Model\Interpolation\BoundingBox;
use AppBundle\Model\Interpolation\GridSize;
use JMS\Serializer\Annotation as JMS;

/**
 * Class GeoTiffProperties
 * @package AppBundle\Model\GeoTiff
 */
class GeoTiffProperties
{
    /**
     * @var GridSize $gridSize
     *
     * @JMS\Groups({"geotiff"})
     */
    protected $gridSize;

    /**
     * @var BoundingBox $boundingBox
     *
     * @JMS\Groups({"geotiff"})
     */
    protected $boundingBox;

    /**
     * @var array $data
     *
     * @JMS\Groups({"geotiff"})
     */
    protected $data;

    /**
     * @var string $colorRelief
     *
     * @JMS\Groups({"geotiff"})
     */
    protected $colorRelief;


    /**
     * GeoTiffProperties constructor.
     * @param Raster $raster
     * @param $colorRelief
     */
    public function __construct(Raster $raster, $colorRelief)
    {
        $this->boundingBox = $raster->getBoundingBox();
        $this->gridSize = $raster->getGridSize();
        $this->data = $raster->getData();
        $this->colorRelief = $colorRelief;
    }
}