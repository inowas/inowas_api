<?php

namespace Inowas\ModflowBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Inowas\ModflowBundle\Data\MapData;
use Inowas\ModflowBundle\Model\GridSize;
use Inowas\ModflowBundle\Model\ModflowModel;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ModelController extends FOSRestController
{

    /**
     * Returns GeoJson for map in models-list.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns GeoJson for map in models-list.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Rest\Get("/models/list/map")
     * @return JsonResponse
     */
    public function getModflowModelsGeoJsonAction(){
        return new JsonResponse(MapData::getGeoJson(), 200, array(), true);
    }

    /**
     * Get list of models.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a new Model.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Rest\Get("/models")
     * @return View
     */
    public function getModflowModelsAction()
    {
        $modelManager = $this->get('inowas.modflow.toolmanager');

        $view = View::create($modelManager->findAllModels())
            ->setStatusCode(200)
            ->setSerializationContext(
                SerializationContext::create()
                    ->setGroups(array('list'))
                    ->enableMaxDepthChecks()
            )
        ;

        return $view;
    }

    /**
     * Create a new Model.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Create a new Model.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Rest\Post("/models")
     * @param ParamFetcher $paramFetcher
     * @RequestParam(name="name", nullable=false, strict=true, description="Name of the new ModflowModel.")
     *
     * @return View
     */
    public function postModflowModelAction(ParamFetcher $paramFetcher)
    {
        $modelManager = $this->get('inowas.modflow.toolmanager');
        $model = $modelManager->createModel();
        $model->setName($paramFetcher->get('name'));
        $modelManager->updateModel($model);

        $view = View::create($model)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('details'))
                ->enableMaxDepthChecks()
            )
        ;

        return $view;
    }

    /**
     * Returns the model details specified by modelId.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns the model details specified by modelId.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the model is not found"
     *   }
     * )
     *
     * @Rest\Get("/models/{id}")
     * @param $id
     * @return View
     * @throws NotFoundHttpException
     */
    public function getModflowModelAction($id)
    {
        $modelManager = $this->get('inowas.modflow.toolmanager');
        $model = $modelManager->findModelById($id);

        if (! $model instanceof ModflowModel){
            throw $this->createNotFoundException(sprintf('Model with id=%s not found.', $id));
        }

        $view = View::create($model)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('details'))
                ->enableMaxDepthChecks()
            )
        ;

        return $view;
    }

    /**
     * @Rest\Put("/models/{id}")
     *
     * Update the model details specified by modelId.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns the model details specified by modelId.",
     *   statusCodes = {
     *     200 = "Returned when successful",
     *     404 = "Returned when the model is not found"
     *   }
     * )
     *
     * @param ParamFetcher $paramFetcher
     * @param $id
     *
     * @RequestParam(name="name", nullable=false, strict=false, description="Model name")
     * @RequestParam(name="description", nullable=false, strict=false, description="Model description")
     * @RequestParam(name="start", nullable=false, strict=false, description="Start date")
     * @RequestParam(name="end", nullable=false, strict=false, description="End date")
     * @RequestParam(name="gridsizeNx", nullable=false, strict=false, description="Gridsize, number of columns")
     * @RequestParam(name="gridsizeNy", nullable=false, strict=false, description="Gridsize, number of rows")
     * @RequestParam(name="soilmodelId", nullable=false, strict=false, description="Soilmodel Id")
     *
     * @return View
     * @throws NotFoundHttpException
     */
    public function putModflowModelAction(ParamFetcher $paramFetcher, $id)
    {
        $model = $this->get('inowas.modflow.toolmanager')->findModelById($id);

        if (! $model instanceof ModflowModel){
            throw $this->createNotFoundException(sprintf('Model with id=%s not found.', $id));
        }

        if ($paramFetcher->get('name')){
            $model->setName($paramFetcher->get('name'));
        }

        if ($paramFetcher->get('description')){
            $model->setDescription($paramFetcher->get('description'));
        }

        if ($paramFetcher->get('start')){
            $model->setStart(new \DateTime($paramFetcher->get('start')));
        }

        if ($paramFetcher->get('end')){
            $model->setEnd(new \DateTime($paramFetcher->get('end')));
        }

        if ($paramFetcher->get('gridsizeNx') && $paramFetcher->get('gridsizeNy')){
            $model->setGridSize(new GridSize($paramFetcher->get('gridsizeNx'), $paramFetcher->get('gridsizeNy')));
        }

        if ($paramFetcher->get('soilmodelId')){
            $model->setSoilmodelId($paramFetcher->get('soilmodelId'));
        }

        $this->get('inowas.modflow.toolmanager')->updateModel($model);

        $view = View::create($model)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('details'))
                ->enableMaxDepthChecks()
            )
        ;

        return $view;
    }
}
