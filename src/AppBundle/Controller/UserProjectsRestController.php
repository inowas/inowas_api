<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserProjectsRestController extends FOSRestController
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
     * @param $username
     *
     * @return View
     */
    public function getUserProjectsAction($username)
    {

        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findOneBy(array(
                'username' => $username
            ));

        if ($user)
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

        if (!$projects) {
            throw $this->createNotFoundException('Projects not found.');
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
        $view->setData($projectList)->setStatusCode(200);

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
        $view->setData($project)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()->enableMaxDepthChecks())
        ;

        return $view;
    }
}
