<?php

namespace AppBundle\Controller;

use AppBundle\Entity\GeologicalLayer;
use AppBundle\Entity\Project;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class GeologicalLayerRestController extends FOSRestController
{    /**
     * Return a list of geological layers by project
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return a list of geological layers by project",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param string $projectId project-id
     *
     * @return View
     */
    public function getProjectGeologicallayersAction($projectId)
    {
        /** @var Project $project */
        $project = $this->getDoctrine()
            ->getRepository('AppBundle:Project')
            ->findOneBy(array(
                'id' => $projectId
            ));

        if (!$project)
        {
            throw $this->createNotFoundException('Project with id='.$projectId.' not found.');
        }

        $geologicalLayers = $this->getDoctrine()
            ->getRepository('AppBundle:GeologicalLayer')
            ->findAllByProjectId($projectId);

        $view = View::create();
        $view->setData($geologicalLayers)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups('modelobjectlist')
            )
        ;
        return $view;
    }

    /**
     * Return a geological layer by id
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return a geological layer by id",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param string $projectId Project-Id
     * @param string $id GeologicalLayer-Id
     *
     * @return View
     */
    public function getProjectGeologicallayerAction($projectId, $id)
    {

        /** @var GeologicalLayer $geologicalLayers */
        $geologicalLayer = $this->getDoctrine()
            ->getRepository('AppBundle:GeologicalLayer')
            ->findByIdAndProjectId($id, $projectId);

        $view = View::create();
        $view->setData($geologicalLayer)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups('layerdetails')
            )
        ;

        return $view;
    }
}
