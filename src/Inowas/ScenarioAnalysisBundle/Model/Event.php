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

    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->createdAt = new \DateTime('now');
    }

    public function __clone()
    {
        $this->id = Uuid::uuid4();
    }

    abstract protected function applyTo(ModflowModel $model);
}
