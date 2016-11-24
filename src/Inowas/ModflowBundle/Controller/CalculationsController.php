<?php

namespace Inowas\ModflowBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\FOSRestController;

use Inowas\ModflowBundle\Model\ModflowModel;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CalculationsController extends FOSRestController
{
    /**
     * * @Get("/calculation/{id}/packages")
     *
     * Return the list of available ModflowPackages from a Calculation Id.
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
    public function getFlopyCalculationPropertiesAction($id)
    {
        $calculation = $this->get('inowas.modflow.calculationmanager')->findById($id);
        return new JsonResponse($calculation->getCalculationProperties(), 200);
    }

    /**
     * * @Get("/calculation/{id}/packages/{packageName}")
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
        $packageManager = $this->get('inowas.flopy.packagemanager');

        return new JsonResponse($packageManager->getPackageData($model, $soilmodel, $packageName), 200);
    }
}
