<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Nelmio\ApiDocBundle\Annotation\ApiDoc as ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;

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
}
