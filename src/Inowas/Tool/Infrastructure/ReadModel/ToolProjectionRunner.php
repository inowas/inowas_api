<?php

declare(strict_types=1);

namespace Inowas\Tool\Infrastructure\ReadModel;

use Inowas\Tool\Infrastructure\Projection\Table;
use Inowas\Tool\Model\Event\ToolInstanceNameWasUpdated;
use Inowas\Tool\Model\Event\ToolInstanceWasCreated;
use Prooph\EventStore\Projection\ProjectionManager;
use Prooph\EventStore\Projection\ReadModelProjector;

class ToolProjectionRunner
{
    /** @var ReadModelProjector */
    private $projection;

    public function __construct(ToolReadModel $toolReadModel, ProjectionManager $projectionManager)
    {
        $this->projection = $projectionManager
            ->createReadModelProjection(Table::TOOL_LIST_READ_MODEL, $toolReadModel)
            ->fromStream('tool_instance_event_stream')
            ->init(function (): array {
                return [
                    'id' => null
                ];
            })
            ->when([
                ToolInstanceWasCreated::class => function ($state, ToolInstanceWasCreated $event) {
                    $this->readModel()->insert(array(
                            'id' => $event->id()->toString(),
                            'application' => '',
                            'project' => '',
                            'tool' => $event->type()->toString(),
                            'user_id' => $event->userId()->toString(),
                            'user_name' => $this->readModel()->getUserNameByUserId($event->userId()),
                            'created_at' => date_format($event->createdAt(), DATE_ATOM)
                        )
                    );
                    $state['id'] = $event->id()->toString();
                    return $state;
                },
                ToolInstanceNameWasUpdated::class => function ($state, ToolInstanceNameWasUpdated $event) {
                    $this->readModel()->update(
                        ['id' => $event->id()->toString()],
                        ['name' => $event->name()->toString()]
                    );

                    $state['id'] = $event->uuid()->toString();
                    return $state;
                }
            ]);
    }

    public function __invoke(bool $keepRunning = true): void
    {
        $this->projection->run($keepRunning);
    }

    public function delete(): void
    {
        $this->projection->delete(true);
    }

    public function reset(): void
    {
        $this->projection->reset();
    }
}
