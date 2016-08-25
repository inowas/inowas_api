<?php

namespace Inowas\PyprocessingBundle\Controller;

use AppBundle\Entity\ModelScenario;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use Inowas\PyprocessingBundle\Model\Modflow\Package\PackageFactory;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ModflowModelPackagesController extends FOSRestController
{

    /**
     * * @Get("/modflowmodel/{id}/packages")
     *
     * Return the list of available ModflowPackages from a ModelId.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return the list of available ModflowPackages from a ModelId.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the model is not found"
     *   }
     * )
     *
     * @param $id
     * @return View
     * @throws NotFoundHttpException
     */
    public function getModflowModelPackagesAction($id)
    {
        $this->findModelById($id);
        $arr = array('mf', 'dis', 'bas', 'lpf', 'pcg', 'oc', 'riv', 'wel', 'rch');

        $view = View::create();
        $view->setData($arr)
            ->setStatusCode(200)
        ;

        return $view;
    }

    /**
     * * @Get("/modflowmodel/{id}/packages/{packageName}")
     *
     * Return the list of available ModflowPackages from a ModelId.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Return the list of available ModflowPackages from a ModelId.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the model is not found"
     *   }
     * )
     *
     * @param $id
     * @param $packageName
     * @return JsonResponse
     * @throws NotFoundHttpException
     */
    public function getModflowModelPackageAction($id, $packageName)
    {
        $model = $this->findModelById($id);

        $package = PackageFactory::create($packageName, $model);
        $response = new JsonResponse();
        $response->setData($package);

        return $response;
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
            ->getRepository('AppBundle:ModelScenario')
            ->findOneBy(array(
                'id' => $id
            ));

        if ($scenario instanceof ModelScenario) {
            return $scenario->getModel();
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