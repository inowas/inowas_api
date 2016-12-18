<?php

namespace Inowas\ScenarioAnalysisBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\UserBundle\Model\UserInterface;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ScenarioAnalysisBundle\Exception\InvalidArgumentException;
use Inowas\ScenarioAnalysisBundle\Exception\InvalidUuidException;
use Inowas\ScenarioAnalysisBundle\Model\ScenarioAnalysis;
use Nelmio\ApiDocBundle\Annotation\ApiDoc as ApiDoc;
use Ramsey\Uuid\Uuid;
use Symfony\Component\Finder\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\JsonResponse;

class ScenarioAnalysisController extends FOSRestController
{
    /**
     * Get ScenarioAnalysis from current user.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get ScenarioAnalysis from current user.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Rest\Get("users/{username}")
     * @param string $username
     * @return JsonResponse
     * @throws AccessDeniedException
     */
    public function getScenariosAnalysisAction($username)
    {
        $userManager = $this->get('fos_user.user_manager');
        $user = $userManager->findUserByUsername($username);

        if (! $user == $this->getUser()){
            throw $this->createAccessDeniedException();
        }

        /** @var UserInterface $user */
        $user = $this->getUser();
        $scenarioAnalysisManager = $this->get('inowas.scenarioanalysis.scenarioanalysismanager');
        $scenarioAnalyses = $scenarioAnalysisManager->findByUserId($user->getId());

        $responseData = [];

        /** @var ScenarioAnalysis $scenarioAnalysis */
        foreach ($scenarioAnalyses as $scenarioAnalysis){
            $model = new \stdClass();
            $model->id = $scenarioAnalysis->getBaseModelId();

            $responseData[] = (object) array(
                'base_model' => $model,
                'scenarios' => $scenarioAnalysis->getScenarios()->toArray()
            );
        }

        return new JsonResponse($responseData);
    }

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
    public function getScenariosAnalysisDetailsAction($modelId)
    {

        if (! Uuid::isValid($modelId)){
            throw new InvalidUuidException();
        }

        /** @var ModflowModel $baseModel */
        $baseModel = $this->get('inowas.modflow.toolmanager')->findModelById($modelId);
        if (!$baseModel instanceof ModflowModel){
            throw new InvalidArgumentException(sprintf('Model with id=%s does not exist.', $modelId));
        }

        /** @var UserInterface $user */
        $user = $this->getUser();
        $scenarioAnalysisManager = $this->get('inowas.scenarioanalysis.scenarioanalysismanager');
        $scenarioAnalysis = $scenarioAnalysisManager
            ->findByUserIdAndBasemodelId($user->getId(), $baseModel->getId()
        );

        if (is_null($scenarioAnalysis)){
            $scenarioAnalysis = $scenarioAnalysisManager->create($user, $baseModel);
        }

        $model = new \stdClass();
        $model->id = $baseModel->getId()->toString();
        $model->name = $baseModel->getName();
        $model->description = $baseModel->getDescription();

        $responseData = [
            'base_model' => $model,
            'scenarios' => $scenarioAnalysis->getScenarios()->toArray()
        ];

        return new JsonResponse($responseData);
    }
}
