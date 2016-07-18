<?php

namespace AppBundle\Model\GeoImage;

use AppBundle\Entity\Raster;
use AppBundle\Model\BoundingBox;
use JMS\Serializer\Annotation as JMS;

/**
 * Class GeoImageProperties
 * @package AppBundle\Model\GeoTiff
 */
class GeoImageProperties implements \JsonSerializable
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

    /**
     * @return mixed
     */
    function jsonSerialize()
    {
        return array(
            'bounding_box' => $this->boundingBox,
            'data' => $this->data,
            'no_data_val' => $this->noDataVal,
            'color_scheme' => $this->colorScheme,
            'target_projection' => $this->targetProjection,
            'output_format' => $this->outputFormat,
            'min' => $this->min,
            'max' => $this->max
        );
    }
}