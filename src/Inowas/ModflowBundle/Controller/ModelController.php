<?php

namespace Inowas\ModflowBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use Inowas\ModflowBundle\Model\ModflowModel;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ModelController extends FOSRestController
{

    /**
     * @Post("/model")
     *
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
     * @param ParamFetcher $paramFetcher
     *
     * @RequestParam(name="name", nullable=false, strict=true, description="Name of the new ModflowModel.")
     *
     * @return JsonResponse
     */
    public function postModflowModelAction(ParamFetcher $paramFetcher)
    {
        $modelManager = $this->get('inowas.modflow.modelmanager');
        $model = $modelManager->create();
        $model->setName($paramFetcher->get('name'));
        $modelManager->update($model);

        $response = new JsonResponse();
        $response->setData($model);
        return $response;
    }

    /**
     * * @Get("/model/{id}")
     *
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
     * @param $id
     * @return JsonResponse
     * @throws NotFoundHttpException
     */
    public function getModflowModelAction($id)
    {
        $modelManager = $this->get('inowas.modflow.modelmanager');
        $model = $modelManager->findById($id);

        if (! $model instanceof ModflowModel){
            throw $this->createNotFoundException(sprintf('Model with id=%s not found.', $id));
        }

        $response = new JsonResponse();
        $response->setData($model);
        return $response;
    }
}
