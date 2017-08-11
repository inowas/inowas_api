<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Inowas\Common\Id\BoundaryId;
use Inowas\Common\Id\ModflowId;
use Inowas\ModflowBundle\Exception\AccessDeniedException;
use Inowas\ModflowBundle\Exception\NotFoundException;
use Inowas\ModflowModel\Model\Command\CalculateModflowModel;
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
     * @throws \InvalidArgumentException
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
        $permissions = $this->get('inowas.user_permissions')->getModelPermissions($userId, $modelId);

        $arr = $model->toArray();
        $arr['permissions'] = $permissions->toString();

        return (new JsonResponse())->setData($arr);
    }

    /**
     * Get list of boundaries from modflowmodel by id.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns a list of all boundaries with ids, types, names, geometry.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param string $id
     * @Rest\Get("/modflowmodels/{id}/boundaries")
     * @return JsonResponse
     * @throws \Inowas\ModflowBundle\Exception\NotFoundException
     */
    public function getModflowModelBoundaryListAction(string $id): JsonResponse
    {
        $this->assertUserIsLoggedInCorrectly();
        $this->assertUuidIsValid($id);
        $modelId = ModflowId::fromString($id);

        $boundaryList = $this->get('inowas.modflowmodel.boundary_manager')->getBoundaryList($modelId);

        if (null === $boundaryList) {
            throw NotFoundException::withMessage(sprintf(
                'ModflowModel with id: \'%s\' not found.', $modelId->toString()
            ));
        }

        return new JsonResponse($boundaryList->toArray());
    }

    /**
     * Get boundary by modelId and boundaryId.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns the boundary.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param string $id
     * @param string $bid
     * @Rest\Get("/modflowmodels/{id}/boundaries/{bid}")
     * @return JsonResponse
     * @throws \Inowas\ModflowBundle\Exception\NotFoundException
     */
    public function getModflowModelBoundaryAction(string $id, string $bid): JsonResponse
    {
        $this->assertUuidIsValid($id);
        $modelId = ModflowId::fromString($id);
        $boundaryId = BoundaryId::fromString($bid);

        $boundary = $this->get('inowas.modflowmodel.boundary_manager')->getBoundary($modelId, $boundaryId);

        if (null === $boundary){
            throw NotFoundException::withMessage(sprintf(
                'ModflowModel with id: \'%s\' and BoundaryId \'%s\' not found.', $modelId->toString(), $boundaryId->toString()
            ));
        }

        return new JsonResponse($boundary->toArray());
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
     * @Rest\Get("/modflowmodels/{id}/stressperiods")
     * @return JsonResponse
     * @throws \Inowas\ModflowBundle\Exception\AccessDeniedException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws NotFoundException
     */
    public function getModflowModelStressPeriodsAction(string $id): JsonResponse
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

        $stressPeriods = $this->container->get('inowas.modflowmodel.manager')->getStressPeriodsByModelId($modelId);
        return new JsonResponse($stressPeriods);
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
        return new RedirectResponse($calculationId);
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
     * @return JsonResponse
     * @throws \Inowas\ModflowBundle\Exception\AccessDeniedException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \Prooph\ServiceBus\Exception\CommandDispatchException
     * @throws \InvalidArgumentException
     */
    public function getSoilModelAction(string $id): JsonResponse
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

        $soilmodel = $this->container->get('inowas.modflowmodel.soilmodel_finder')->getSoilmodel($modelId);
        return new JsonResponse($soilmodel);
    }

    /**
     * Calculate the model.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Calculate the model.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param string $id
     * @Rest\Post("/modflowmodels/{id}/calculate")
     * @return JsonResponse
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException
     * @throws \Symfony\Component\DependencyInjection\Exception\ServiceCircularReferenceException
     * @throws \Prooph\ServiceBus\Exception\CommandDispatchException
     */
    public function postModflowModelCalculateAction(string $id): JsonResponse
    {
        $this->assertUuidIsValid($id);
        $modelId = ModflowId::fromString($id);

        $this->container->get('prooph_service_bus.modflow_command_bus')->dispatch(
            CalculateModflowModel::forModflowModelFromTerminal($modelId)
        );

        return new JsonResponse('CalculationCommand sent');
    }

}
