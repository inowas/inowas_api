<?php

declare(strict_types=1);

namespace Tests\Inowas\ModflowBundle\Functional;

use Inowas\Common\Calculation\CalculationState;
use Inowas\Common\Id\CalculationId;
use Inowas\ModflowModel\Infrastructure\Projection\Calculation\ModflowCalculationFinder;
use Inowas\ModflowModel\Infrastructure\Projection\ModelList\ModelFinder;
use Inowas\ModflowModel\Model\AMQP\ModflowCalculationResponse;
use Inowas\ModflowModel\Model\Command\CalculateModflowModel;
use Inowas\ModflowModel\Model\Command\UpdateCalculationState;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Tests\Inowas\ModflowBundle\EventSourcingBaseTest;

class ModflowModelCalculationEventSourcingTest extends EventSourcingBaseTest
{
    /**
     * @throws \Exception
     */
    public function test_dispatch_relevant_calculation_commands(): void
    {
        $modelId = ModflowId::generate();
        $ownerId = UserId::generate();

        /** @var ModflowCalculationFinder $calculationFinder */
        $calculationFinder = $this->container->get('inowas.modflowmodel.modflow_calculation_finder');

        /** @var ModelFinder $modelFinder */
        $modelFinder = $this->container->get('inowas.modflowmodel.model_finder');

        $this->createModelWithOneLayer($ownerId, $modelId);

        $this->commandBus->dispatch(CalculateModflowModel::forModflowModelWitUserId($ownerId, $modelId));
        $response = $calculationFinder->getModelsCalculationsDetailsByModelId($modelId);
        $this->assertEquals($response['model_id'], $modelId->toString());
        $this->assertEquals($response['calculation_id'], null);
        $this->assertEquals($response['state'], CalculationState::CALCULATION_PROCESS_STARTED);
        $this->assertEquals(null, $modelFinder->getCalculationIdByModelId($modelId));

        $calculationId = CalculationId::fromString('test123');
        $this->commandBus->dispatch(UpdateCalculationState::preprocessingFinished($modelId, $calculationId));
        $response = $calculationFinder->getModelsCalculationsDetailsByModelId($modelId);
        $this->assertEquals($response['model_id'], $modelId->toString());
        $this->assertEquals($response['calculation_id'], $calculationId->toString());
        $this->assertEquals($response['state'], CalculationState::PREPROCESSING_FINISHED);
        $this->assertEquals($calculationId, $modelFinder->getCalculationIdByModelId($modelId));

        $calculationId = CalculationId::fromString('test123');
        $this->commandBus->dispatch(UpdateCalculationState::queued($modelId, $calculationId));
        $response = $calculationFinder->getModelsCalculationsDetailsByModelId($modelId);
        $this->assertEquals($response['model_id'], $modelId->toString());
        $this->assertEquals($response['calculation_id'], $calculationId->toString());
        $this->assertEquals($response['state'], CalculationState::QUEUED);
        $this->assertEquals($calculationId, $modelFinder->getCalculationIdByModelId($modelId));

        $calculationId = CalculationId::fromString('test123');
        $response = ModflowCalculationResponse::fromArray([
            'status_code' => '200',
            'model_id' => $modelId->toString(),
            'calculation_id' => $calculationId->toString(),
            'message' => 'message'
        ]);
        $this->commandBus->dispatch(UpdateCalculationState::calculationFinished($modelId, $calculationId, $response));
        $response = $calculationFinder->getModelsCalculationsDetailsByModelId($modelId);
        $this->assertEquals($response['model_id'], $modelId->toString());
        $this->assertEquals($response['calculation_id'], $calculationId->toString());
        $this->assertEquals($response['state'], CalculationState::CALCULATION_FINISHED);
    }
}
