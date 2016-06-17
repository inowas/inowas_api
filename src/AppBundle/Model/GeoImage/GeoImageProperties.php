<?php

namespace AppBundle\Model\GeoImage;

use AppBundle\Entity\Raster;
use AppBundle\Model\Interpolation\BoundingBox;
use JMS\Serializer\Annotation as JMS;

/**
 * Class GeoImageProperties
 * @package AppBundle\Model\GeoTiff
 */
class GeoImageProperties
{
    const COLOR_RELIEF_ELEVATION = 'elevation';

    /**
     * @var BoundingBox $boundingBox
     *
     * @JMS\Groups({"geoimage"})
     */
    protected $boundingBox;

    /**
     * @var array $data
     *
     * @JMS\Groups({"geoimage"})
     */
    protected $data;

    /**
     * @var  integer
     *
     * @JMS\Groups({"geoimage"})
     */
    protected $noDataVal;

    /**
     * @var string $colorRelief
     *
     * @JMS\Groups({"geoimage"})
     */
    protected $colorRelief;

    /**
     * @var integer
     *
     * @JMS\Groups({"geoimage"})
     */
    protected $targetProjection;

    /**
     * @var string
     *
     * @JMS\Groups({"geoimage"})
     */
    protected $outputFormat;

    /**
     * GeoImageProperties constructor.
     * @param Raster $raster
     * @param $activeCells
     * @param $colorRelief
     * @param $targetProjection
     * @param $outputFormat
     */
    public function __construct(Raster $raster, $activeCells, $colorRelief, $targetProjection, $outputFormat)
    {
        $this->boundingBox = $raster->getBoundingBox();
        $this->data = $raster->getFilteredData($activeCells);
        $this->noDataVal = $raster->getNoDataVal();
        $this->colorRelief = $colorRelief;
        $this->targetProjection = $targetProjection;
        $this->outputFormat = $outputFormat;
    }
}