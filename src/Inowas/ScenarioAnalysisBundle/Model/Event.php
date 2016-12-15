<?php

namespace Inowas\ScenarioAnalysisBundle\Model;

use Inowas\ModflowBundle\Model\ModflowModel;
use Ramsey\Uuid\Uuid;

abstract class Event
{
    /**
     * @var Uuid
     */
    protected $id;

    /**
     * @var string
     */
    protected $payload;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    final protected function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->createdAt = new \DateTime('now');
    }

    abstract protected function applyTo(ModflowModel $model);
}
