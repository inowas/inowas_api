<?php

namespace Inowas\ScenarioAnalysisBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\UserBundle\Model\UserInterface;
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
     * @Rest\Get("/models/{modelId}")
     * @param $modelId
     * @return JsonResponse
     * @throws InvalidUuidException
     * @throws InvalidArgumentException
     */
    public function getScenariosAnalysisModelScenariosAction($modelId)
    {

        if (! Uuid::isValid($modelId)){
            throw new InvalidUuidException();
        }

        /** @var UserInterface $user */
        $user = $this->getUser();

        return new JsonResponse($this->get('inowas.model_scenarios_finder')
            ->findByUserAndBaseModelId(
                UserId::fromString($user->getId()->toString()),
                ModflowId::fromString($modelId)
            ));
    }
}
