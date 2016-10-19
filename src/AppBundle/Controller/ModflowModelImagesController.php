<?php

namespace AppBundle\Controller;

use Ramsey\Uuid\Uuid;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

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
}
