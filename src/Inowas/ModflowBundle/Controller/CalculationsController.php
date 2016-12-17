<?php

namespace Inowas\ModflowBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;

use FOS\RestBundle\View\View;
use Inowas\ModflowBundle\Exception\InvalidArgumentException;
use Inowas\ModflowBundle\Model\Modflow;
use Inowas\ModflowBundle\Model\ModflowModel;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CalculationsController extends FOSRestController
{
    /**
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
     * @Rest\Get("/calculation/{id}/packages")
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
     * @Rest\Get("/calculation/{modelId}/packages/{packageName}")
     * @param $modelId
     * @param $packageName
     * @return JsonResponse
     * @throws NotFoundHttpException
     */
    public function getModflowModelPackageAction($modelId, $packageName)
    {
        /** @var ModflowModel $model */
        $model = $this->get('inowas.modflow.modelmanager')->findById($modelId);
        $soilmodel = $this->get('inowas.soilmodel.soilmodelmanager')->findById($model->getSoilmodelId());
        $packageManager = $this->get('inowas.flopy.packagemanager');

        return new JsonResponse($packageManager->getPackageData($model, $soilmodel, $packageName), 200);
    }

    /**
     * Sends the command to calculate the model by model-id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Sends the command to calculate the model by model-id",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the Calculation-Id is not found"
     *   }
     * )
     *
     *
     * @Rest\Post("/calculation/{modelId}")
     * @param $modelId
     * @return View
     */
    public function postModflowCalculation($modelId){
        $modflowController = $this->get('inowas.modflow.modelmanager');
        $model = $modflowController->findById(Uuid::fromString($modelId));

        if (! $model instanceof ModflowModel){
            throw new InvalidArgumentException(sprintf('Model with id=%s not available', $modelId));
        }

        $modflow = $this->getDoctrine()->getRepository('InowasModflowBundle:Modflow')
            ->findOneBy(array(
                'modflowModel' => $model,
                'userId' => $this->getUser()->getId()
            ));

        if (! $modflow instanceof Modflow){
            throw new InvalidArgumentException(sprintf('Model %s available, but not owner', $modelId));
        }

        $cc = $this->get('inowas.modflow.calculationmanager');
        $calculation = $cc->create($modflow->getModflowModel());
        $cc->update($calculation);

        $view = View::create($calculation)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('details'))
            )
        ;

        return $view;
    }

    /**
     * Sends the command to calculate the model by model-id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Sends the command to calculate the model by model-id",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the Calculation-Id is not found"
     *   }
     * )
     *
     *
     * @Rest\Get("/calculation/{modelId}")
     * @param $modelId
     * @return View
     */
    public function getModflowCalculation($modelId){
        $calculation = $this->get('inowas.modflow.calculationmanager')->findByModelId($modelId);
        $view = View::create($calculation)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('details'))
            )
        ;

        return $view;
    }
}
