<?php

namespace AppBundle\Controller;

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
            throw $this->createNotFoundException('Area with id='.$id.' not found.');
        }



    }
}
