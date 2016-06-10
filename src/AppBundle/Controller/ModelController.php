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
        return new Response('Models');
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

        return $this->render(':inowas:model.html.twig', array(
                'model' => $model
            )
        );
    }
}
