<?php

declare(strict_types=1);

namespace Inowas\ModflowBundle\Controller;

use FOS\RestBundle\Controller\Annotations as Rest;
use Inowas\Common\DateTime\DateTime;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Modflow\LayerValues;
use Inowas\Common\Modflow\StressPeriods;
use Inowas\Common\Modflow\TotalTimes;
use Inowas\ModflowBundle\Exception\NotFoundException;
use Inowas\ModflowCalculation\Model\Command\CalculateModflowModelCalculation;
use Inowas\ModflowCalculation\Model\Command\CreateModflowModelCalculation;
use Inowas\ModflowCalculation\Model\Command\UpdateCalculationStressperiods;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class CalculationsController extends InowasRestController
{
    /**
     * Creates a new calculation.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Creates a new calculation.",
     *   statusCodes = {
     *     302 = "Redirect to the calculation"
     *   }
     * )
     *
     * @Rest\Post("/calculations")
     * @param Request $request
     * @return RedirectResponse
     */
    public function postCalculationsAction(Request $request): RedirectResponse
    {
        $userId = $this->getUserId();
        $calculationId = ModflowId::generate();

        $content = $this->getContentAsArray($request);
        $this->assertContainsKey('model_id', $content);
        $modelId = ModflowId::fromString($content['model_id']);

        $this->assertContainsKey('start_date_time', $content);
        $startDateTime = DateTime::fromAtom($content['start_date_time']);

        $this->assertContainsKey('end_date_time', $content);
        $endDateTime = DateTime::fromAtom($content['end_date_time']);

        $commandBus = $this->get('prooph_service_bus.modflow_command_bus');
        $commandBus->dispatch(CreateModflowModelCalculation::byUserWithModelId($calculationId, $userId, $modelId, $startDateTime, $endDateTime));

        return new RedirectResponse(
            $this->generateUrl('get_calculation_details', array('id' => $modelId->toString())),
            302
        );
    }

    /**
     * Get calculation details by calculationId.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns the calculation details of a calculation by calculationId.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param string $id
     * @Rest\Get("/calculations/{id}")
     * @return JsonResponse
     */
    public function getCalculationDetailsAction(string $id): JsonResponse
    {
        $this->assertUuidIsValid($id);
        $calculationId = ModflowId::fromString($id);

        $result = $this->get('inowas.modflowcalculation.calculation_list_finder')->findCalculationById($calculationId);

        if (null === $result) {
            throw NotFoundException::withMessage(sprintf(
                'Calculation with id: \'%s\' not found.', $calculationId->toString()
            ));
        }

        return new JsonResponse($result);
    }

    /**
     * Starts a calculation by calculationId.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Starts a calculation by calculationId.",
     *   statusCodes = {
     *     302 = "Redirect to the calculation"
     *   }
     * )
     *
     * @Rest\Post("/calculations/{id}/calculate")
     * @param string $id
     * @return RedirectResponse
     */
    public function postCalculationCalculateAction(string $id): RedirectResponse
    {
        $userId = $this->getUserId();

        $this->assertUuidIsValid($id);
        $calculationId = ModflowId::fromString($id);

        $commandBus = $this->get('prooph_service_bus.modflow_command_bus');
        $commandBus->dispatch(CalculateModflowModelCalculation::byUserWithCalculationId($userId, $calculationId));

        return new RedirectResponse(
            $this->generateUrl('get_calculation_details', array('id' => $calculationId->toString())),
            302
        );
    }

    /**
     * Get calculation stressperiods by calculationId.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns the stressperiods of a calculation by id.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param string $id
     * @Rest\Get("/calculations/{id}/stressperiods")
     * @return JsonResponse
     */
    public function getCalculationStressperiodsAction(string $id): JsonResponse
    {
        $this->assertUuidIsValid($id);
        $calculationId = ModflowId::fromString($id);

        $stressPeriods = $this->get('inowas.modflowcalculation.calculation_configuration_finder')->getStressPeriodsFromCalculationById($calculationId);

        if (! $stressPeriods instanceof StressPeriods) {
            throw NotFoundException::withMessage(sprintf(
                'Calculation with id: \'%s\' not found.', $calculationId->toString()
            ));
        }

        return new JsonResponse($stressPeriods);
    }

    /**
     * Put calculation stressperiods by calculationId.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Updated the stressperiods of a calculation by id.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param string $id
     * @param Request $request
     * @Rest\Put("/calculations/{id}/stressperiods")
     * @return RedirectResponse
     */
    public function putCalculationStressperiodsAction(string $id, Request $request): RedirectResponse
    {
        $userId = $this->getUserId();

        $this->assertUuidIsValid($id);
        $calculationId = ModflowId::fromString($id);

        $content = $this->getContentAsArray($request);

        $this->assertContainsKey('start_date_time', $content);
        $this->assertContainsKey('end_date_time', $content);
        $this->assertContainsKey('time_unit', $content);
        $this->assertContainsKey('stress_periods', $content);

        $stressPeriods = StressPeriods::createFromArray($content);

        $commandBus = $this->get('prooph_service_bus.modflow_command_bus');
        $commandBus->dispatch(UpdateCalculationStressperiods::byUserWithCalculationId($userId, $calculationId, $stressPeriods));

        return new RedirectResponse(
            $this->generateUrl('get_calculation_stressperiods', array('id' => $calculationId->toString())),
            302
        );
    }

    /**
     * Get calculation result times by calculationId.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns the calculation result times of a calculation by id.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param string $id
     * @Rest\Get("/calculations/{id}/results/times")
     * @return JsonResponse
     */
    public function getCalculationResultsTimesAction(string $id): JsonResponse
    {
        $this->assertUuidIsValid($id);
        $calculationId = ModflowId::fromString($id);

        $totalTimes = $this->get('inowas.modflowcalculation.calculation_results_finder')->getTotalTimesFromCalculationById($calculationId);

        if (! $totalTimes instanceof TotalTimes) {
            throw NotFoundException::withMessage(sprintf(
                'Calculation with id: \'%s\' not found.', $calculationId->toString()
            ));
        }

        return new JsonResponse($totalTimes);
    }

    /**
     * Get calculation layerValues by calculationId.
     *
     * @ApiDoc(
     *   resource = true,
     *   description = "Returns the calculation layerValues of a calculation by id.",
     *   statusCodes = {
     *     200 = "Returned when successful"
     *   }
     * )
     *
     * @param string $id
     * @Rest\Get("/calculations/{id}/results/layervalues")
     * @return JsonResponse
     */
    public function getCalculationResultsLayerValuesAction(string $id): JsonResponse
    {
        $this->assertUuidIsValid($id);
        $calculationId = ModflowId::fromString($id);

        $layerValues = $this->get('inowas.modflowcalculation.calculation_results_finder')->findLayerValues($calculationId);

        if (! $layerValues instanceof LayerValues) {
            throw NotFoundException::withMessage(sprintf(
                'Calculation with id: \'%s\' not found.', $calculationId->toString()
            ));
        }

        return new JsonResponse($layerValues);
    }
}
