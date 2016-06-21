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
     * @var  integer|string
     *
     * @JMS\Groups({"geoimage"})
     */
    protected $min;

    /**
     * @var  integer|string
     *
     * @JMS\Groups({"geoimage"})
     */
    protected $max;

    /**
     * @var string $colorRelief
     *
     * @JMS\Groups({"geoimage"})
     */
    protected $colorScheme;

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
     * @param $colorScheme
     * @param $targetProjection
     * @param $outputFormat
     * @param string $min
     * @param string $max
     */
    public function __construct(Raster $raster, $activeCells, $colorScheme, $targetProjection, $outputFormat, $min="10%",  $max="90%")
    {
        $this->boundingBox = $raster->getBoundingBox();
        $this->data = $raster->getFilteredData($activeCells);
        $this->noDataVal = $raster->getNoDataVal();
        $this->colorScheme = $colorScheme;
        $this->targetProjection = $targetProjection;
        $this->outputFormat = $outputFormat;
        $this->min = $min;
        $this->max = $max;
    }
}