<?php

namespace Inowas\ModflowBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\FOSRestController;
use Inowas\ModflowBundle\Model\Factory\PackageFactory;
use Inowas\ModflowBundle\Model\ModflowModel;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ModelPackagesController extends FOSRestController
{

    /**
     * * @Get("/model/{id}/packages")
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
     * @return JsonResponse
     * @throws NotFoundHttpException
     */
    public function getModflowModelPackagesAction($id)
    {
        /** @var ModflowModel $model */
        $model = $this->get('inowas.modflow.modelmanager')->findById($id);

        $response = new JsonResponse();
        $response->setData($model->getCalculationProperties());

        return $response;
    }

    /**
     * * @Get("/model/{id}/packages/{packageName}")
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
        /** @var ModflowModel $model */
        $model = $this->get('inowas.modflow.modelmanager')->findById($id);
        $soilmodel = $this->get('inowas.soilmodel.soilmodelmanager')->findById($model->getSoilmodelId());
        $package = PackageFactory::create($packageName, $model, $soilmodel);
        $response = new JsonResponse();
        $response->setData($package);

        return $response;
    }
}
