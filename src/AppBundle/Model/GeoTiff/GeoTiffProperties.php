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
    const COLOR_RELIEF_ELEVATION = 'elevation';

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
     * @var  integer
     *
     * @JMS\Groups({"geotiff"})
     */
    protected $noDataVal;

    /**
     * @var string $colorRelief
     *
     * @JMS\Groups({"geotiff"})
     */
    protected $colorRelief;

    /**
     * @var integer
     *
     * @JMS\Groups({"geotiff"})
     */
    protected $targetProjection;

    /**
     * @var string
     *
     * @JMS\Groups({"geotiff"})
     */
    protected $outputFormat;

    /**
     * GeoTiffProperties constructor.
     * @param Raster $raster
     * @param string $colorRelief
     * @param int $targetProjection
     * @param string $outputFormat
     */
    public function __construct(Raster $raster, $colorRelief, $targetProjection, $outputFormat)
    {
        $this->boundingBox = $raster->getBoundingBox();
        $this->gridSize = $raster->getGridSize();
        $this->data = $raster->getData();
        $this->noDataVal = $raster->getNoDataVal();
        $this->colorRelief = $colorRelief;
        $this->targetProjection = $targetProjection;
        $this->outputFormat = $outputFormat;
    }
}