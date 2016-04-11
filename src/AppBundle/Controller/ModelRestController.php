<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class ModelRestController extends FOSRestController
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
    public function getUserModelsAction($username)
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
            $models = $this->getDoctrine()
                ->getRepository('AppBundle:ModFlowModel')
                ->findBy(
                    array('owner' => $user),
                    array('id' => 'ASC')
                );
        } else
        {
            $models = $this->getDoctrine()
                ->getRepository('AppBundle:ModFlowModel')
                ->findBy(
                    array(
                        'owner' => $user,
                        'public' => true
                    ),
                    array('id' => 'ASC')
                );
        }

        $view = View::create();
        $view->setData($models)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('list'))
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
     * @param $modelId
     *
     * @return View
     */
    public function getModelAction($modelId)
    {

        if ($this->isGranted('ROLE_ADMIN'))
        {
            $model = $this->getDoctrine()
                ->getRepository('AppBundle:ModFlowModel')
                ->findOneBy(array(
                    'id' => $modelId
                ));
        } elseif ($this->isGranted('ROLE_USER'))
        {
            $model = $this->getDoctrine()
                ->getRepository('AppBundle:ModFlowModel')
                ->findOneBy(array(
                    'id' => $modelId,
                    'owner' => $this->getUser()
                ));
        } else
        {
            $model = $this->getDoctrine()
                ->getRepository('AppBundle:ModFlowModel')
                ->findOneBy(array(
                    'id' => $modelId,
                    'public' => true
                ));
        }

        if (!$model) {
            throw $this->createNotFoundException('Project not found.');
        }

        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modeldetails');

        $view = View::create();
        $view->setData($model)
            ->setStatusCode(200)
            ->setSerializationContext($serializationContext)
        ;

        return $view;
    }
}
