<?php

namespace Inowas\ModflowBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\FOSRestController;

use FOS\RestBundle\Request\ParamFetcher;
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
     * @Get("/calculation/{id}/packages")
     * @param $id
     * @return JsonResponse
     * @throws NotFoundHttpException
     */
    public function getModflowModelCalculationPropertiesAction($id)
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
     * @Get("/calculation/{modelId}/packages/{packageName}")
     * @param $modelId
     * @param $packageName
     * @return JsonResponse
     * @throws NotFoundHttpException
     */
    public function getModflowModelPackageAction($modelId, $packageName)
    {
        /** @var ModflowModel $model */
        $model = $this->get('inowas.modflow.toolmanager')->findModelById($modelId);
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
     * @Post("/calculation/{modelId}")
     * @param $modelId
     * @return View
     */
    public function postModflowModelCalculationAction($modelId)
    {
        $modflowController = $this->get('inowas.modflow.toolmanager');
        $model = $modflowController->findModelById(Uuid::fromString($modelId));

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


        $flopy = $this->get('inowas.flopy');
        $calculation = $flopy->addModelToQueue($modflow->getModflowModel());
        $flopy->calculate($calculation);

        $view = View::create($calculation)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('details'))
            )
        ;

        return $view;
    }

    /**
     * Get the state of current calculation by Model-Id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get the state of current calculation by Model-Id",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the Calculation-Id is not found"
     *   }
     * )
     *
     *
     * @Get("/calculation/{modelId}")
     * @param $modelId
     * @return View
     */
    public function getModflowModelCalculationAction($modelId){
        $calculation = $this->get('inowas.modflow.calculationmanager')->findByModelId($modelId);
        $view = View::create($calculation)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('details'))
            )
        ;

        return $view;
    }

    /**
     * Posts the headData
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Update the model area specified by modelId.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the model is not found"
     *   }
     * )
     *
     * @Post("/calculation/{id}/heads")
     *
     * @param ParamFetcher $paramFetcher
     * @param $id
     *
     * @RequestParam(name="heads", nullable=false, strict=false, description="The heads array")
     * @RequestParam(name="totim", nullable=false, strict=false, description="The totim")
     *
     * @return View
     */
    public function postModflowModelHeadsAction(ParamFetcher $paramFetcher, $id)
    {

        $heads = json_decode($paramFetcher->get('heads'));
        $totim = $paramFetcher->get('totim');

        $model = $this->get('inowas.modflow.toolmanager')->findModelById($id);
        $headsManager = $this->get('inowas.modflow.headsmanager');

        foreach ( $heads as $layerNumber => $data){
            $headsManager->addHead($model, $totim, $layerNumber, $data);
        }

        $view = View::create('OK')->setStatusCode(200);
        return $view;
    }
}
