<?php

namespace Inowas\ScenarioAnalysisBundle\Model;

use Symfony\Component\Validator\Constraints\Uuid;

class Event
{
    /**
     * @var Uuid
     */
    protected $id;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var integer
     */
    protected $order;

    /**
     * @var string
     */
    protected $payload;

    /**
     * @return Uuid
     */
    public function getId(): Uuid
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }

    /**
     * @param string $type
     * @return Event
     */
    public function setType(string $type): Event
    {
        $this->type = $type;
        return $this;
    }

    /**
     * @return int
     */
    public function getOrder(): int
    {
        return $this->order;
    }

    /**
     * @param int $order
     * @return Event
     */
    public function setOrder(int $order): Event
    {
        $this->order = $order;
        return $this;
    }

    /**
     * @return string
     */
    public function getPayload(): string
    {
        return $this->payload;
    }

    /**
     * @param string $payload
     * @return Event
     */
    public function setPayload(string $payload): Event
    {
        $this->payload = $payload;
        return $this;
    }
}
