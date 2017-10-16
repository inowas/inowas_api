<?php

declare(strict_types=1);

namespace Tests\Inowas\ModflowBundle\Functional;

use Inowas\Common\Modflow\Description;
use Inowas\Common\Modflow\Name;
use Inowas\Common\Id\UserId;
use Inowas\Common\Status\Visibility;
use Inowas\Tool\Model\Command\CloneToolInstance;
use Inowas\Tool\Model\Command\CreateToolInstance;
use Inowas\Tool\Model\Command\DeleteToolInstance;
use Inowas\Tool\Model\Command\UpdateToolInstance;
use Inowas\Tool\Model\ToolData;
use Inowas\Tool\Model\ToolId;
use Inowas\Tool\Model\ToolType;
use Tests\Inowas\ModflowBundle\EventSourcingBaseTest;

class ToolInstanceEventSourcingTest extends EventSourcingBaseTest
{
    /**
     * @test
     */
    public function it_can_create_a_tool_instance(): void
    {
        $toolId = ToolId::generate();
        $ownerId = UserId::generate();
        $toolType = ToolType::fromString('T02');
        $name = Name::fromString('ToolName');
        $description = Description::fromString('ToolDescription');
        $data = ToolData::fromArray([1,3,5, 'test' => '1, 3, 5']);

        $this->commandBus->dispatch(CreateToolInstance::newWithAllParams($ownerId, $toolId, $toolType, $name, $description, $data));

        $tool = $this->container->get('inowas.tool.tools_finder')->findById($toolId);
        $this->assertNotNull($tool);

        $this->assertArrayHasKey('id', $tool);
        $this->assertEquals($toolId->toString(), $tool['id']);

        $this->assertArrayHasKey('name', $tool);
        $this->assertEquals($name->toString(), $tool['name']);

        $this->assertArrayHasKey('description', $tool);
        $this->assertEquals($description->toString(), $tool['description']);

        $this->assertArrayHasKey('project', $tool);
        $this->assertArrayHasKey('application', $tool);

        $this->assertArrayHasKey('tool', $tool);
        $this->assertEquals($toolType->toString(), $tool['tool']);

        $this->assertArrayHasKey('created_at', $tool);

        $this->assertArrayHasKey('user_id', $tool);
        $this->assertEquals($ownerId->toString(), $tool['user_id']);

        $this->assertArrayHasKey('user_name', $tool);

        $this->assertArrayHasKey('public', $tool);
        $this->assertEquals(1, $tool['public']);

        $this->assertArrayHasKey('data', $tool);
        $this->assertEquals($data->toArray(), $tool['data']);
    }

    /**
     * @test
     */
    public function it_can_clone_a_tool_instance(): void
    {
        $toolId = ToolId::generate();
        $ownerId = UserId::generate();
        $toolType = ToolType::fromString('T02');
        $name = Name::fromString('ToolName');
        $description = Description::fromString('ToolDescription');
        $data = ToolData::fromArray([1,3,5, 'test' => '1, 3, 5']);
        $this->commandBus->dispatch(CreateToolInstance::newWithAllParams($ownerId, $toolId, $toolType, $name, $description, $data));

        $tool = $this->container->get('inowas.tool.tools_finder')->findById($toolId);
        $this->assertNotNull($tool);

        $newToolId = ToolId::generate();
        $this->commandBus->dispatch(CloneToolInstance::newWithAllParams($ownerId, $toolId, $newToolId));

        $tool = $this->container->get('inowas.tool.tools_finder')->findById($newToolId);
        $this->assertNotNull($tool);
    }

    /**
     * @test
     */
    public function it_can_update_a_tool_instance(): void
    {
        $toolId = ToolId::generate();
        $ownerId = UserId::generate();
        $toolType = ToolType::fromString('T01');
        $name = Name::fromString('ToolName');
        $description = Description::fromString('ToolDescription');
        $data = ToolData::fromArray([1,3,5, 'test' => '1, 3, 5']);
        $visibility = Visibility::fromBool(false);
        $this->commandBus->dispatch(CreateToolInstance::newWithAllParams($ownerId, $toolId, $toolType, $name, $description, $data, $visibility));

        $name = Name::fromString('UpdatedToolName');
        $description = Description::fromString('UpdatedToolDescription');
        $data = ToolData::fromArray([1,3,5, 'updatedTest' => '1, 3, 5']);
        $visibility = Visibility::fromBool(true);
        $this->commandBus->dispatch(UpdateToolInstance::newWithAllParams($ownerId, $toolId, $name, $description, $data, $visibility));

        $tool = $this->container->get('inowas.tool.tools_finder')->findById($toolId);
        $this->assertNotNull($tool);

        $this->assertArrayHasKey('id', $tool);
        $this->assertEquals($toolId->toString(), $tool['id']);

        $this->assertArrayHasKey('name', $tool);
        $this->assertEquals($name->toString(), $tool['name']);

        $this->assertArrayHasKey('description', $tool);
        $this->assertEquals($description->toString(), $tool['description']);

        $this->assertArrayHasKey('project', $tool);
        $this->assertArrayHasKey('application', $tool);

        $this->assertArrayHasKey('tool', $tool);
        $this->assertEquals($toolType->toString(), $tool['tool']);

        $this->assertArrayHasKey('created_at', $tool);

        $this->assertArrayHasKey('user_id', $tool);
        $this->assertEquals($ownerId->toString(), $tool['user_id']);

        $this->assertArrayHasKey('user_name', $tool);

        $this->assertArrayHasKey('public', $tool);
        $this->assertEquals(1, $tool['public']);

        $this->assertArrayHasKey('data', $tool);
        $this->assertEquals($data->toArray(), $tool['data']);
    }

    /**
     * @test
     */
    public function it_can_delete_a_tool_instance(): void
    {
        $toolId = ToolId::generate();
        $ownerId = UserId::generate();
        $toolType = ToolType::fromString('T01');
        $name = Name::fromString('ToolName');
        $description = Description::fromString('ToolDescription');
        $data = ToolData::fromArray([1,3,5, 'test' => '1, 3, 5']);
        $this->commandBus->dispatch(CreateToolInstance::newWithAllParams($ownerId, $toolId, $toolType, $name, $description, $data));

        $tool = $this->container->get('inowas.tool.tools_finder')->findById($toolId);
        $this->assertNotNull($tool);

        $this->commandBus->dispatch(DeleteToolInstance::newWithAllParams($ownerId, $toolId));

        $tool = $this->container->get('inowas.tool.tools_finder')->findById($toolId);
        $this->assertNull($tool);
    }
}
