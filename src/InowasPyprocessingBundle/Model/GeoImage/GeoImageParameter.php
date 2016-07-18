<?php

namespace InowasPyprocessingBundle\Model\GeoImage;

use AppBundle\Entity\Raster;
use AppBundle\Model\BoundingBox;
use AppBundle\Model\GridSize;
use InowasPyprocessingBundle\Service\GeoImage;
use InowasPyprocessingBundle\Exception\InvalidArgumentException;

class GeoImageParameter
{
    /** @var Raster $raster **/
    protected $raster;

    /** @var integer */
    protected $activeCells;

    /** @var float */
    protected $min;

    /** @var float */
    protected $max;

    /** @var string */
    protected $fileFormat;

    /** @var string */
    protected $colorRelief;

    /** @var integer */
    protected $targetProjection;

    public function __construct(Raster $raster, $activeCells=null, $min=null, $max=null, $fileFormat="png", $colorRelief=GeoImage::COLOR_RELIEF_JET, $targetProjection=4326)
    {
        if (!$raster->getBoundingBox() instanceof BoundingBox) {
            throw new InvalidArgumentException('Raster has no valid BoundingBox-Element');
        }

        if (!$raster->getGridSize() instanceof GridSize) {
            throw new InvalidArgumentException('Raster has no valid Gridsize-Element');
        }

        if (count($raster->getData()) != $raster->getGridSize()->getNY()){
            throw new InvalidArgumentException(sprintf('RasterData rowCount differs from GridSize rowCount', count($raster->getData()), $raster->getGridSize()->getNY()));
        }

        if (count($raster->getData()[0]) != $raster->getGridSize()->getNX()){
            throw new InvalidArgumentException(sprintf('RasterData colCount differs from GridSize colCount', count($raster->getData()[0]), $raster->getGridSize()->getNX()));
        }

        $this->raster = $raster;
        $this->activeCells = $activeCells;
        $this->min = $min;
        $this->max = $max;
        $this->fileFormat = $fileFormat;
        $this->colorRelief = $colorRelief;
        $this->targetProjection = $targetProjection;
    }

    /**
     * @return Raster
     */
    public function getRaster()
    {
        return $this->raster;
    }

    /**
     * @return int
     */
    public function getActiveCells()
    {
        return $this->activeCells;
    }

    /**
     * @return float
     */
    public function getMin()
    {
        return $this->min;
    }

    /**
     * @return float
     */
    public function getMax()
    {
        return $this->max;
    }

    /**
     * @return string
     */
    public function getFileFormat()
    {
        return $this->fileFormat;
    }

    /**
     * @return string
     */
    public function getColorRelief()
    {
        return $this->colorRelief;
    }

    /**
     * @return int
     */
    public function getTargetProjection()
    {
        return $this->targetProjection;
    }
}