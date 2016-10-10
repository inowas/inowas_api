<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Property;
use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ramsey\Uuid\Uuid;

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
     *
     * @QueryParam(name="startDate", nullable=false, strict=false, description="startDate")
     * @QueryParam(name="endDate", nullable=false, strict=false, description="endDate")
     *
     * @return View
     */
    public function getPropertyAction($id)
    {

        try {
            $uuid = Uuid::fromString($id);
        } catch (\InvalidArgumentException $e) {
            throw $this->createNotFoundException(sprintf('Property with id=%s not found.', $id));
        }

        $property = $this->getDoctrine()
            ->getRepository('AppBundle:Property')
            ->findOneBy(array(
                'id' => $uuid
            ));

        if (! $property instanceof Property) {
            throw $this->createNotFoundException(sprintf('Property with id=%s not found.', $id));
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
