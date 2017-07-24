<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Inowas\Common\Id\ModflowId;
use Inowas\ModflowBundle\Exception\AccessDeniedException;
use Inowas\ModflowBundle\Exception\NotFoundException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;

/** @noinspection LongInheritanceChainInspection */
class ModflowModelController extends InowasRestController
{
    # MODEL-LISTING
    /**
     * Get list of modflow models from user
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get list of modflow models from user.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Rest\Get("/modflowmodels")
     * @return JsonResponse
     */
    public function getModflowModelsAction(): JsonResponse
    {
        return new JsonResponse(
            $this->get('inowas.modflowmodel.model_finder')->findModelsByBaseUserId($this->getUserId())
        );
    }

    /**
     * Get list of all public modflow models
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get list of my Modflow Models.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @Rest\Get("/modflowmodels/public")
     * @return JsonResponse
     */
    public function getPublicModflowModelsAction(): JsonResponse
    {
        $this->assertUserIsLoggedInCorrectly();

        return new JsonResponse(
            $this->get('inowas.modflowmodel.model_finder')->findPublicModels()
        );
    }

    /**
     * Get details of modflow model by id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get details of modflow model by id.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param string $id
     * @Rest\Get("/modflowmodels/{id}")
     * @return JsonResponse
     * @throws \Inowas\ModflowBundle\Exception\AccessDeniedException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws NotFoundException
     */
    public function getModflowModelAction(string $id): JsonResponse
    {
        $this->assertUuidIsValid($id);
        $modelId = ModflowId::fromString($id);
        $userId = $this->getUserId();

        if (! $this->get('inowas.modflowmodel.model_finder')->userHasReadAccessToModel($userId, $modelId)) {
            throw AccessDeniedException::withMessage(
                sprintf(
                    'Model not found or user with Id %s does not have access to model with id %s',
                    $userId->toString(),
                    $modelId->toString()
                )
            );
        }

        $model = $this->container->get('inowas.modflowmodel.manager')->findModel($modelId);
        return new JsonResponse($model);
    }

    /**
     * Get details of last calculation of modflow model by id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get details of last calculation of modflow model by id.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param string $id
     * @Rest\Get("/modflowmodels/{id}/calculation")
     * @return RedirectResponse
     * @throws \InvalidArgumentException
     * @throws NotFoundException
     */
    public function getModflowModelCalculationAction(string $id): RedirectResponse
    {
        $this->assertUuidIsValid($id);
        $modelId = ModflowId::fromString($id);
        $calculationId = $this->get('inowas.modflowmodel.model_finder')->getCalculationIdByModelId($modelId);

        return new RedirectResponse(
            $this->generateUrl('get_calculation_details', array('id' => $calculationId->toString())),
            302
        );
    }

    /* Soilmodel */
    /**
     * Get soilmodel details of modflowModel by id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Get soilmodel details of modflowModel by id.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param string $id
     * @Rest\Get("/modflowmodels/{id}/soilmodel")
     * @return RedirectResponse
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \Prooph\ServiceBus\Exception\CommandDispatchException
     * @throws \InvalidArgumentException
     */
    public function getSoilModelAction(string $id): RedirectResponse
    {
        $this->assertUuidIsValid($id);
        $modelId = ModflowId::fromString($id);

        $soilmodelId = $this->container->get('inowas.modflowmodel.model_finder')->getSoilmodelIdByModelId($modelId);

        return new RedirectResponse(
            $this->generateUrl('get_soilmodel', array('id' => $soilmodelId->toString())),
            302
        );
    }
}
