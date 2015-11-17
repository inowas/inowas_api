<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class ModelObjectRestController extends FOSRestController
{
    /**
     * Return the overall modelobjects-list.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return the overall modelobjects-list.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @return View
     */
    public function getModelobjectsAction()
    {
        $projects = $this->getDoctrine()->getRepository('AppBundle:ModelObject')
            ->findAll();

        $projectList = array();
        foreach ($projects as $project)
        {
            $projectListItem = array(
                'id' => $project->getId()
            );

            $projectList[] = $projectListItem;
        }

        $view = View::create();
        $view->setData($projectList)->setStatusCode(200);

        return $view;
    }

    /**
     * Return a modelobject by id
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return a modelobject by id",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param string $id project-id
     *
     * @return View
     */
    public function getModelobjectAction($id)
    {

        $project = $this->getDoctrine()
            ->getRepository('AppBundle:ModelObject')
            ->findOneBy(array(
                'id' => $id
            ));

        if (!$project) {
            throw $this->createNotFoundException('Modelobject not found.');
        }

        $view = View::create();
        $view->setData($project)->setStatusCode(200);

        return $view;
    }


}
