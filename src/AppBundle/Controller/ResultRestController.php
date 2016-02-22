<?php

namespace AppBundle\Controller;

use AppBundle\Model\PropertyFactory;
use AppBundle\Model\PropertyTimeValueFactory;
use AppBundle\Model\PropertyTypeFactory;
use AppBundle\Model\PropertyValueFactory;
use AppBundle\Model\RasterBandFactory;
use AppBundle\Model\RasterFactory;

use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class ResultRestController extends FOSRestController
{
    /**
     * Return an area by id
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return an area by id",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
    /**
     * Creates a new Ticket from the submitted data.<br/>
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Creates a new Ticket from the submitted data.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     400 = "Returned when the form has errors"
     *   }
     * )
     *
     * @param ParamFetcher $paramFetcher
     *
     * @RequestParam(name="id", nullable=false, strict=true, description="Area-Id (Modelobject) where to store the results")
     * @RequestParam(name="width", nullable=false, strict=true, description="Dimension width")
     * @RequestParam(name="height", nullable=false, strict=true, description="Dimension height")
     * @RequestParam(name="upperLeftX", nullable=false, strict=true, description="UpperLeft corner X")
     * @RequestParam(name="upperLeftY", nullable=false, strict=true, description="UpperLeft corner Y")
     * @RequestParam(name="scaleX", nullable=false, strict=true, description="Rotation ScaleX")
     * @RequestParam(name="scaleY", nullable=false, strict=true, description="Rotation ScaleY")
     * @RequestParam(name="skewX", nullable=false, strict=true, description="Rotation SkewX, default=0", default=0)
     * @RequestParam(name="skewY", nullable=false, strict=true, description="Rotation SkewY, default=0", default=0)
     * @RequestParam(name="srid", nullable=false, strict=true, description="SRID", default=4326)
     * @RequestParam(name="bandPixelType", nullable=false, strict=true, description="Pixeltype of Band")
     * @RequestParam(name="bandInitValue", nullable=true, strict=true, description="InitValue of Band, default=null", default=null)
     * @RequestParam(name="bandNoDataVal", nullable=true, strict=true, description="NoDataValue of Band, default=null", default=null)
     * @RequestParam(name="data", nullable=false, strict=true, description="Data in JSON-Format")
     * @RequestParam(name="date", nullable=true, strict=true, description="Date, default null", default=null)
     *
     * @return View
     */
    public function postResultsAction(ParamFetcher $paramFetcher)
    {
        $area = $this->getDoctrine()
            ->getRepository('AppBundle:Area')
            ->findOneBy(array(
                'id' => $paramFetcher->get('id')
            ));

        if (!$area)
        {
            throw $this->createNotFoundException('Area with id='.$paramFetcher->get('id').' not found.');
        }

        $rasterObject = RasterFactory::createModel();
        $rasterObject
            ->setWidth($paramFetcher->get('width'))
            ->setHeight($paramFetcher->get('height'))
            ->setUpperLeftX($paramFetcher->get('upperLeftX'))
            ->setUpperLeftY($paramFetcher->get('upperLeftY'))
            ->setScaleX($paramFetcher->get('scaleX'))
            ->setScaleY($paramFetcher->get('scaleY'))
            ->setSkewX($paramFetcher->get('skewX'))
            ->setSkewY($paramFetcher->get('skewY'))
            ->setSrid($paramFetcher->get('srid'))
        ;

        $rasterBand = RasterBandFactory::create();
        $rasterBand
            ->setPixelType($paramFetcher->get("bandPixelType"))
            ->setInitValue($paramFetcher->get("bandInitValue"))
            ->setNoDataVal($paramFetcher->get("bandNoDataVal"))
            ->setData(json_decode($paramFetcher->get("data")));

        $property = PropertyFactory::setTypeAndModelObject(PropertyTypeFactory::setName('gwHead'), $area);


        if ($paramFetcher->get('date') == null)
        {
            $value = PropertyValueFactory::create();
        } else {
            $value = PropertyTimeValueFactory::create();
            $value->setDatetime(new \DateTime($paramFetcher->get('date')));
        }

        // We have to create the row in the rasters-table first
        $rasterEntity = RasterFactory::createEntity();
        $this->getDoctrine()->getManager()->persist($rasterEntity);
        $this->getDoctrine()->getManager()->flush();

        $value->setRaster($rasterEntity);
        $property->addValue($value);
        $area->addProperty($property);

        $this->getDoctrine()->getManager()->persist($value);
        $this->getDoctrine()->getManager()->persist($property);
        $this->getDoctrine()->getManager()->persist($area);
        $this->getDoctrine()->getManager()->flush();

        $this->getDoctrine()->getRepository('AppBundle:Raster')
            ->addRasterWithData($rasterEntity);
    }
}
