<?php

declare(strict_types=1);

namespace Tests\Inowas\ModflowBundle\Functional;

use Inowas\Common\Id\CalculationId;
use Inowas\ModflowModel\Model\AMQP\FlopyCalculationResponse;
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

        $this->createModelWithOneLayer($ownerId, $modelId);

        $this->commandBus->dispatch(CalculateModflowModel::forModflowModelWitUserId($ownerId, $modelId));
        $row = $this->container->get('inowas.modflowmodel.calculation_process_finder')->getNextRow();
        $this->assertEquals([
            'id' => 1,
            'model_id' => $modelId->toString(),
            'calculation_id' => null,
            'state' => 1
        ], $row[0]);

        $calculationId = CalculationId::fromString('test123');
        $this->commandBus->dispatch(UpdateCalculationState::preprocessingFinished($modelId, $calculationId));
        $row = $this->container->get('inowas.modflowmodel.calculation_process_finder')->getNextRow();
        $this->assertEquals([
            'id' => 1,
            'model_id' => $modelId->toString(),
            'calculation_id' => $calculationId->toString(),
            'state' => 3
        ], $row[0]);

        $calculationId = CalculationId::fromString('test123');
        $this->commandBus->dispatch(UpdateCalculationState::queued($modelId, $calculationId));
        $row = $this->container->get('inowas.modflowmodel.calculation_process_finder')->getNextRow();
        $this->assertEquals([
            'id' => 1,
            'model_id' => $modelId->toString(),
            'calculation_id' => $calculationId->toString(),
            'state' => 3
        ], $row[0]);

        $calculationId = CalculationId::fromString('test123');
        $response = FlopyCalculationResponse::fromArray([
            'status_code' => '200',
            'model_id' => $modelId->toString(),
            'calculation_id' => $calculationId->toString(),
            'message' => 'message'
        ]);
        $this->commandBus->dispatch(UpdateCalculationState::calculationFinished($modelId, $calculationId, $response));
        $row = $this->container->get('inowas.modflowmodel.calculation_process_finder')->getNextRow();
        $this->assertCount(0, $row);
    }
}
