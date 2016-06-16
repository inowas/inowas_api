<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Response;

class ModelController extends Controller
{
    /**
     * @Route("/models/modflow")
     */
    public function modelsAction()
    {
        $models = $this->getDoctrine()->getRepository('AppBundle:ModFlowModel')
            ->findAll();

        return $this->render(':inowas/model/modflow:models.html.twig', array(
                'models' => $models
            )
        );
    }

    /**
     * @Route("/models/modflow/{id}")
     */
    public function modelAction($id)
    {
        $model = $this->getDoctrine()->getRepository('AppBundle:ModFlowModel')
            ->findOneBy(array(
                'id' => $id
            ));

        return $this->render(':inowas/model/modflow:model.html.twig', array(
                'model' => $model
            )
        );
    }
}
