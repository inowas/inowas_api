<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ModelController extends Controller
{
    /**
     * @Route("/models/modflow", name="modflow_model_list")
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

        if (null === $model){
            return $this->redirectToRoute('modflow_model_list');
        }

        return $this->render(':inowas/model/modflow:model.html.twig', array(
                'model' => $model
            )
        );
    }
}
