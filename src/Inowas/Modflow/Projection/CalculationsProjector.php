<?php

namespace Inowas\Modflow\Projection;

use Inowas\Modflow\Model\Event\ModflowCalculationResultWasAdded;
use Inowas\Modflow\Model\Event\ModflowCalculationWasCreated;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class CalculationsProjector
{
    private $cache;

    /** @var array */
    private $calculations = [];

    public function __construct() {
        $this->cache = new FilesystemAdapter('app.cache');
        $this->cache->clear();
    }

    public function onModflowCalculationWasCreated(ModflowCalculationWasCreated $event)
    {
        $this->calculations[$event->calculationId()->toString()] = [];
    }

    public function onModflowCalculationResultWasAdded(ModflowCalculationResultWasAdded $event)
    {
        $this->calculations[$event->calculationId()->toString()] = $event->result();
    }

    public function getData(): array
    {
        return $this->calculations;
    }
}
