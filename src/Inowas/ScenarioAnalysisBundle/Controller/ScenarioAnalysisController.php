<?php

namespace Inowas\ScenarioAnalysisBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\View\View;
use FOS\UserBundle\Model\UserInterface;
use Inowas\ModflowBundle\Model\ModflowModel;
use Inowas\ScenarioAnalysisBundle\Exception\InvalidArgumentException;
use Inowas\ScenarioAnalysisBundle\Exception\InvalidUuidException;
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
     * @return View
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
        $scenarioAnalysis = $scenarioAnalysisManager->findByUserId($user->getId());
        $view = View::create($scenarioAnalysis)->setStatusCode(200);
        return $view;
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
     * @param $baseModelId
     * @return JsonResponse
     * @throws InvalidUuidException
     * @throws InvalidArgumentException
     */
    public function getScenariosAnalysisDetailsAction($baseModelId)
    {
        if (! Uuid::isValid($baseModelId)){
            throw new InvalidUuidException();
        }

        $baseModel = $this->get('inowas.modflow.modelmanager')->findById($baseModelId);
        if (!$baseModel instanceof ModflowModel){
            throw new InvalidArgumentException(sprintf('Model with id=%s does not exist.', $baseModelId));
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

        $responseData = [
            [ 'baseModel' =>
                [
                    'id' => $baseModel->getId()->toString(),
                    'name' => $baseModel->getName(),
                    'description' => $baseModel->getDescription()
                ]
            ],
            [ 'scenarios' => $scenarioAnalysis->getScenarios() ]
        ];

        return new JsonResponse($responseData);
    }


}
