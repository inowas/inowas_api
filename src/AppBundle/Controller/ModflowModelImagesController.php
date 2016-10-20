<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ModflowModelScenario;
use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;

class ModflowModelImagesController extends Controller
{
    /**
     * @Route("/models/modflow/{id}/image.png", name="modflow_model_image")
     * @Method({"GET"})
     *
     * @param $id
     * @return BinaryFileResponse
     */
    public function getModflowmodelImageAction($id)
    {

        if (!Uuid::isValid($id)){
            throw $this->createNotFoundException('Model with id='.$id.' not found.');
        }

        $path = $this->get('kernel')->getRootDir() . '/../var/data/modflow/' . $id . '/image.png';

        if (! file_exists($path)){
            $path = $this->get('kernel')->getRootDir() . '/../web/img/emptyModel.png';
        }

        $response = new BinaryFileResponse($path);
        $response->headers->set('Content-Type', 'image/png');

        return $response;
    }

    /**
     * @Route("/models/modflow/{id}/map", name="modflow_model_map")
     * @Method({"GET"})
     *
     * @param $id
     * @return Response
     */
    public function getModflowmodelMapAction($id)
    {
        $model = $this->findModelById($id);

        return $this->render('inowas/model/modflow/model.map.html.twig', array(
                'model' => $model,
                'user' => $model->getOwner()
            )
        );
    }

    /**
     * @param $id
     * @return \AppBundle\Entity\AbstractModel
     */
    private function findModelById($id)
    {

        if (!Uuid::isValid($id)){
            throw $this->createNotFoundException('Model with id='.$id.' not found.');
        }

        $scenario = $this->getDoctrine()
            ->getRepository('AppBundle:ModflowModelScenario')
            ->findOneBy(array(
                'id' => $id
            ));

        if ($scenario instanceof ModflowModelScenario) {
            return $scenario;
        }

        $model = $this->getDoctrine()
            ->getRepository('AppBundle:ModFlowModel')
            ->findOneBy(array(
                'id' => $id,
            ));

        if (!$model) {
            throw $this->createNotFoundException('Model not found.');
        }

        return $model;
    }
}
