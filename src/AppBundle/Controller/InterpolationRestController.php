<?php

namespace AppBundle\Controller;

use AppBundle\Model\Interpolation\BoundingBox;
use AppBundle\Model\Interpolation\GridSize;
use AppBundle\Model\Interpolation\KrigingInterpolation;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class InterpolationRestController extends FOSRestController
{
    /**
     * Make an InterpolationRequest
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Make an InterpolationRequest",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @return View
     */
    public function postInterpolationAction()
    {
        $krigingInterpolation = new KrigingInterpolation(new GridSize(12, 13), new BoundingBox(1.2, 1.2, 2.1, .2));

        $view = View::create();
        $view->setData($krigingInterpolation)
            ->setStatusCode(200)
        ;

        return $view;
    }
}
