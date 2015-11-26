<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class ProjectRestController extends FOSRestController
{
    /**
     * Return the overall project list from a user.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return the overall Project list from a user",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param $username
     * @return View
     */
    public function getUserProjectsAction($username)
    {
        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findOneBy(array(
                'username' => $username
            ));

        if (!$user)
        {
            throw $this->createNotFoundException('User with username '.$username.' not found.');
        }

        if ($this->getUser() === $user || $this->isGranted('ROLE_ADMIN'))
        {
            $projects = $this->getDoctrine()
                ->getRepository('AppBundle:Project')
                ->findBy(array(
                    'owner' => $user
                ));
        } else
        {
            $projects = $this->getDoctrine()
                ->getRepository('AppBundle:Project')
                ->findBy(array(
                    'owner' => $user,
                    'public' => true
                ));
        }

        $view = View::create();
        $view->setData($projects)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('projectList'))
            )
        ;

        return $view;
    }

    /**
     * Return the project-information by id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return the project-information by id.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the user is not found"
     *   }
     * )
     *
     * @param $projectId
     *
     * @return View
     */
    public function getProjectAction($projectId)
    {

        if ($this->isGranted('ROLE_ADMIN'))
        {
            $project = $this->getDoctrine()
                ->getRepository('AppBundle:Project')
                ->findOneBy(array(
                    'id' => $projectId
                ));
        } elseif ($this->isGranted('ROLE_USER'))
        {
            $project = $this->getDoctrine()
                ->getRepository('AppBundle:Project')
                ->findOneBy(array(
                    'id' => $projectId,
                    'owner' => $this->getUser()
                ));
        } else
        {
            $project = $this->getDoctrine()
                ->getRepository('AppBundle:Project')
                ->findOneBy(array(
                    'id' => $projectId,
                    'public' => true
                ));
        }

        if (!$project) {
            throw $this->createNotFoundException('Project not found.');
        }

        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('projectDetails');

        $view = View::create();
        $view->setData($project)
            ->setStatusCode(200)
            ->setSerializationContext($serializationContext)
        ;

        return $view;
    }
}
