<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Request\ParamFetcher;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\Validator\ConstraintViolationList;

class ProjectRestController extends FOSRestController
{
    /**
     * Return the overall project list.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return the overall Project List",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @return View
     */
    public function getProjectsAction()
    {
        $projects = $this->getDoctrine()->getRepository('AppBundle:Project')
            ->findAll();

        if (!$projects) {
            throw $this->createNotFoundException('Data not found.');
        }

        $projectList = array();
        foreach ($projects as $project)
        {
            $projectListItem = array(
                'id' => $project->getId(),
                'name' => $project->getName()
            );

            $projectList[] = $projectListItem;
        }

        $view = View::create();
        $view->setData($projects)->setStatusCode(200);

        return $view;
    }

    /**
     * Return a project by id
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return a project by id",
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
    public function getProjectAction($id)
    {

        $project = $this->getDoctrine()
            ->getRepository('AppBundle:Project')
            ->findOneBy(array(
                'id' => $id
            ));

        if (!$project) {
            throw $this->createNotFoundException('Project not found.');
        }

        $view = View::create();
        $view->setData($project)->setStatusCode(200);

        return $view;
    }


}
