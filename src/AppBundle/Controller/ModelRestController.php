<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ConstantHeadBoundary;
use AppBundle\Entity\GeneralHeadBoundary;
use AppBundle\Entity\Stream;
use AppBundle\Entity\Well;
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

        if (!$user) {
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
            throw $this->createNotFoundException('Model not found.');
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

    /**
     * Returns a list of all Boundaries by ModflowModel-Id
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns a list of all Boundaries by ModflowModel-Id.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the ModflowModel is not found"
     *   }
     * )
     *
     * @param $modelId
     *
     * @return View
     */
    public function getModflowmodelBoundariesAction($modelId)
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
            throw $this->createNotFoundException('Model not found.');
        }

        $boundaries = $model->getBoundaries();

        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modelobjectdetails');

        $view = View::create();
        $view->setData($boundaries)
            ->setStatusCode(200)
            ->setSerializationContext($serializationContext)
        ;

        return $view;
    }

    /**
     * Returns a list of all Constant Head Boundaries by ModflowModel-Id
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns a list of all Constant Head Boundaries by ModflowModel-Id.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the ModflowModel is not found"
     *   }
     * )
     *
     * @param $modelId
     *
     * @return View
     */
    public function getModflowmodelConstant_headAction($modelId)
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
            throw $this->createNotFoundException('Model not found.');
        }

        $rivers = array();
        $boundaries = $model->getBoundaries();
        foreach ($boundaries as $boundary) {
            if ($boundary instanceof ConstantHeadBoundary) {
                $rivers[] = $boundary;
            }
        }

        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modelobjectdetails');

        $view = View::create();
        $view->setData($rivers)
            ->setStatusCode(200)
            ->setSerializationContext($serializationContext)
        ;

        return $view;
    }

    /**
     * Returns a list of all General Head Boundaries by ModflowModel-Id
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns a list of all General Head Boundaries by ModflowModel-Id.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the ModflowModel is not found"
     *   }
     * )
     *
     * @param $modelId
     *
     * @return View
     */
    public function getModflowmodelGeneral_headAction($modelId)
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
            throw $this->createNotFoundException('Model not found.');
        }

        $rivers = array();
        $boundaries = $model->getBoundaries();
        foreach ($boundaries as $boundary) {
            if ($boundary instanceof GeneralHeadBoundary) {
                $rivers[] = $boundary;
            }
        }

        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modelobjectdetails');

        $view = View::create();
        $view->setData($rivers)
            ->setStatusCode(200)
            ->setSerializationContext($serializationContext)
        ;

        return $view;
    }

    /**
     * Returns a list of all Wells by ModflowModel-Id
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns a list of all Wells by ModflowModel-Id.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the ModflowModel is not found"
     *   }
     * )
     *
     * @param $modelId
     *
     * @return View
     */
    public function getModflowmodelWellsAction($modelId)
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
            throw $this->createNotFoundException('Model not found.');
        }

        $wells = array();
        $boundaries = $model->getBoundaries();
        foreach ($boundaries as $boundary) {
            if ($boundary instanceof Well) {
                $wells[] = $boundary;
            }
        }

        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modelobjectdetails');

        $view = View::create();
        $view->setData($wells)
            ->setStatusCode(200)
            ->setSerializationContext($serializationContext)
        ;

        return $view;
    }

    /**
     * Returns a list of all Rivers by ModflowModel-Id
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns a list of all Rivers by ModflowModel-Id.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the ModflowModel is not found"
     *   }
     * )
     *
     * @param $modelId
     *
     * @return View
     */
    public function getModflowmodelRiversAction($modelId)
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
            throw $this->createNotFoundException('Model not found.');
        }

        $rivers = array();
        $boundaries = $model->getBoundaries();
        foreach ($boundaries as $boundary) {
            if ($boundary instanceof Stream) {
                $rivers[] = $boundary;
            }
        }

        $serializationContext = SerializationContext::create();
        $serializationContext->setGroups('modelobjectdetails');

        $view = View::create();
        $view->setData($rivers)
            ->setStatusCode(200)
            ->setSerializationContext($serializationContext)
        ;

        return $view;
    }
}
