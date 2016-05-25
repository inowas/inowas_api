<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class PropertyRestController extends FOSRestController
{
    /**
     * Returns a property by id
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns a property by id",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param string $id property-id
     * @param ParamFetcher $paramFetcher
     *
     * @QueryParam(name="startDate", nullable=false, strict=false, description="startDate")
     * @QueryParam(name="endDate", nullable=false, strict=false, description="endDate")
     *
     * @return View
     */
    public function getPropertyAction($id, $paramFetcher)
    {

        $property = $this->getDoctrine()
            ->getRepository('AppBundle:Property')
            ->findOneBy(array(
                'id' => $id
            ));

        if (!$property)
        {
            throw $this->createNotFoundException('Property with id='.$id.' not found.');
        }

        $startDate = null;
        $endDate = null;

        if ($paramFetcher->get('startDate'))
        {
            $startDate = new \DateTime($paramFetcher->get('startDate'));
        }

        if ($paramFetcher->get('endDate'))
        {
            $endDate = new \DateTime($paramFetcher->get('endDate'));
        }

        $view = View::create();
        $view->setData($property)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups('modelobjectdetails')
            )
        ;

        return $view;
    }
}
