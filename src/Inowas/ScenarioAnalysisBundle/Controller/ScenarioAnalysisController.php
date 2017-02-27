<?php

namespace Inowas\ScenarioAnalysisBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use Inowas\Modflow\Model\ModflowId;
use Inowas\Modflow\Model\UserId;
use Inowas\ScenarioAnalysisBundle\Exception\InvalidArgumentException;
use Inowas\ScenarioAnalysisBundle\Exception\InvalidUuidException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc as ApiDoc;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\JsonResponse;

class ScenarioAnalysisController extends FOSRestController
{
    /**
     * Get ScenarioAnalysis detail by BaseModelId.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get ScenarioAnalysis details from current user and basemodelId.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Rest\Get("/{baseModelId}")
     * @param $baseModelId
     * @return JsonResponse
     * @throws InvalidUuidException
     * @throws InvalidArgumentException
     */
    public function getScenariosAnalysisModelScenariosAction($baseModelId)
    {
        if (! Uuid::isValid($baseModelId)){
            throw new InvalidUuidException();
        }

        return new JsonResponse($this->get('inowas.model_scenarios_finder')
            ->findByUserAndBaseModelId(
                UserId::fromString($this->getUser()->getId()->toString()),
                ModflowId::fromString($baseModelId)
            ));
    }

    /**
     * Get Modeldetails by baseModelId.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get Modeldetails by baseModelIs.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Rest\Get("/model/{baseModelId}")
     * @param $baseModelId
     * @return JsonResponse
     * @throws InvalidUuidException
     * @throws InvalidArgumentException
     */
    public function getScenarioAnalysisBaseModelDetailsAction($baseModelId)
    {
        if (! Uuid::isValid($baseModelId)){
            throw new InvalidUuidException();
        }

        return new JsonResponse($this->get('inowas.modflow_projection.model_details_finder')
            ->findByBaseModelId(
                ModflowId::fromString($baseModelId)
            )
        );
    }

    /**
     * Get Boundaries by modelId.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get Boundaries by modelId.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Rest\Get("/model/{modelId}")
     * @param $modelId
     * @return JsonResponse
     * @throws InvalidUuidException
     * @throws InvalidArgumentException
     */
    public function getScenarioAnalysisModelBoundaries($modelId)
    {
        if (! Uuid::isValid($modelId)){
            throw new InvalidUuidException();
        }

        return new JsonResponse($this->get('inowas.modflow_projection.boundaries')
            ->findByBaseModelId(
                ModflowId::fromString($baseModelId)
            )
        );
    }
}
