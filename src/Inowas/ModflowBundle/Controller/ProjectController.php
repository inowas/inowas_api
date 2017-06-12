<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Inowas\ModflowBundle\Exception\AccessDeniedException;
use Inowas\ModflowBundle\Exception\InvalidArgumentException;
use Inowas\ModflowBundle\Exception\NotFoundException;
use Inowas\ModflowBundle\Exception\UserNotAuthenticatedException;
use Inowas\Project\Model\ApplicationType;
use Inowas\Project\Model\ProjectId;
use Inowas\ScenarioAnalysis\Model\Command\CloneScenarioAnalysis;
use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisId;
use Nelmio\ApiDocBundle\Annotation\ApiDoc as ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

/** @noinspection LongInheritanceChainInspection */
class ProjectController extends InowasRestController
{

    /**
     * Get list of my projects.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get list of my scenarioAnalysis-projects.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Rest\Get("/projects")
     * @return JsonResponse
     */
    public function getMyProjectsAction(): JsonResponse
    {
        $userId = $this->getUserId();
        $result = $this->get('inowas.projects.projects_finder')->findByUserId($userId);
        return new JsonResponse($result);
    }

    /**
     * Get list of all public scenarioAnalysis-projects.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get list of all public scenarioAnalysis-projects.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Rest\Get("/projects/public")
     * @return JsonResponse
     */
    public function getPublicProjectsAction(): JsonResponse
    {
        $result = $this->get('inowas.projects.projects_finder')->findPublic();
        return new JsonResponse($result);
    }

    /**
     * Get list of all public scenarioAnalysis-projects.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get list of all public scenarioAnalysis-projects.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Rest\Post("/projects/{id}/clone")
     * @param string $id
     * @return RedirectResponse
     * @throws \Inowas\ModflowBundle\Exception\AccessDeniedException
     * @throws \Inowas\ModflowBundle\Exception\InvalidArgumentException
     * @throws \Prooph\ServiceBus\Exception\CommandDispatchException
     * @throws \InvalidArgumentException
     * @throws NotFoundException
     */
    public function cloneProjectAction(string $id): RedirectResponse
    {
        $userId = $this->getUserId();

        $this->assertUuidIsValid($id);
        $projectId = ProjectId::fromString($id);

        $projectsFinder = $this->get('inowas.projects.projects_finder');

        /** @var ApplicationType $applicationType */
        $applicationType = $projectsFinder->getApplicationTypeById($projectId);

        if (!$applicationType instanceof ApplicationType) {
            throw InvalidArgumentException::withMessage(sprintf(
                'The project with id %s was not found', $projectId->toString()
            ));
        }

        if (!$applicationType instanceof ApplicationType) {
            throw InvalidArgumentException::withMessage(sprintf(
                'The project with id %s was not found', $projectId->toString()
            ));
        }

        if (!ApplicationType::isValid($applicationType)) {
            throw InvalidArgumentException::withMessage(sprintf(
                'The ApplicationType %s is not valid. Available types are: %s',
                $applicationType->toString(),
                implode(', ', ApplicationType::$availableTypes)
            ));
        }

        if (! $projectsFinder->canBeClonedByUser($projectId, $userId)){
            throw AccessDeniedException::withMessage(sprintf(
                'Access denied to clone Project with id %s.',
                $projectId->toString()
            ));
        }

        switch ($applicationType->toString()) {
            case ApplicationType::SCENARIOANALYSIS:
                $this->get('prooph_service_bus.modflow_command_bus')->dispatch(CloneScenarioAnalysis::byUserWithId(
                    $userId, ScenarioAnalysisId::fromString($projectId->toString())
                ));
                break;
        }

        return new RedirectResponse(
            $this->generateUrl('get_my_projects'),
            302
        );
    }
}
