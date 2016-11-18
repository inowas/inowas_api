<?php

namespace Inowas\ModflowBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\RequestParam;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Request\ParamFetcher;
use FOS\RestBundle\View\View;
use Inowas\ModflowBundle\Model\Boundary\Boundary;
use Inowas\ModflowBundle\Model\ModflowModel;
use JMS\Serializer\SerializationContext;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ModelBoundaryController extends FOSRestController
{

    /**
     * @Post("/model/{id}/boundary")
     *
     * Create a new Model.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Add a new boundary to the model.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param $id
     * @param ParamFetcher $paramFetcher
     *
     * @RequestParam(name="type", nullable=false, strict=true, description="BoundaryType. Available types are: chd, ghb, rch, riv, wel")
     * @RequestParam(name="name", nullable=false, strict=true, description="Name of the new Boundary.")
     *
     * @return View
     */
    public function postModflowModelBoundaryAction($id, ParamFetcher $paramFetcher)
    {
        $model = $this->get('inowas.modflow.modelmanager')->findById($id);

        $boundary = $this->get('inowas.modflow.boundarymanager')->create($paramFetcher->get('type'));
        $model->addBoundary($boundary);
        $this->get('inowas.modflow.modelmanager')->update($model);

        $view = View::create($boundary)
            ->setStatusCode(200)
            ->setSerializationContext(SerializationContext::create()
                ->setGroups(array('details'))
            )
        ;

        return $view;
    }

    /**
     * * @Get("/boundary/{id}")
     *
     * Returns the boundary details specified by boundary-ID.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns the boundary details by id.",
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
    public function getBoundaryAction($id)
    {
        $manager = $this->get('inowas.modflow.boundarymanager');
        $boundary = $manager->findById($id);

        if (! $boundary instanceof Boundary){
            throw $this->createNotFoundException(sprintf('Boundary with id=%s not found.', $id));
        }

        $response = new JsonResponse();
        $response->setData($boundary);
        return $response;
    }

    /**
     * * @Get("/model/{id}/boundaries")
     *
     * Returns all boundaries from a Model specified by model id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns all boundaries from a Model specified by model id",
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
    public function getModelBoundariesAction($id)
    {
        $manager = $this->get('inowas.modflow.modelmanager');
        $model = $manager->findById($id);

        if (! $model instanceof ModflowModel){
            throw $this->createNotFoundException(sprintf('Model with id=%s not found.', $id));
        }

        $response = new JsonResponse();
        $response->setData($model->getBoundaries());
        return $response;
    }
}
