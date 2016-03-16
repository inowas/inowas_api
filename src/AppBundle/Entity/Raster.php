<?php
/**
 * More Documentation here:
 *
 * http://postgis.net/docs/manual-dev/RT_ST_MakeEmptyRaster.html
 *
 * raster ST_MakeEmptyRaster(raster rast);
 * raster ST_MakeEmptyRaster(integer width, integer height, float8 upperleftx, float8 upperlefty, float8 scalex, float8 scaley, float8 skewx, float8 skewy, integer srid=unknown);
 * raster ST_MakeEmptyRaster(integer width, integer height, float8 upperleftx, float8 upperlefty, float8 pixelsize);
 *
 *
 * http://postgis.net/docs/manual-2.2/RT_ST_AddBand.html
 *
 *
 */

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use AppBundle\Model\Raster as RasterModel;

/**
 * Raster
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RasterRepository")
 * @ORM\Table(name="rasters")
 */
class Raster
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var RasterModel $raster
     *
     * @ORM\Column(name="rast", type="raster", nullable=true)
     * @JMS\Type("AppBundle\Model\Raster")
     */
    private $raster;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set raster
     *
     * @param RasterModel $raster
     *
     * @return Raster
     */
    public function setRaster(RasterModel $raster)
    {
        $this->raster = $raster;

        return $this;
    }

    /**
     * Get raster
     *
     * @return RasterModel
     */
    public function getRaster()
    {
        return $this->raster;
    }
}
