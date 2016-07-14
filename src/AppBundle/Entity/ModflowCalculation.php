<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\Uuid;

/**
 * ModflowCalculation
 *
 * @ORM\Table(name="modflow_calculation")
 * @ORM\Entity()
 */
class ModflowCalculation
{

    const STATE_IN_QUEUE = 0;
    const STATE_RUNNING = 1;
    const STATE_FINISHED_SUCCESSFUL = 11;
    const STATE_FINISHED_WITH_ERRORS = 12;

    /**
     * @var Uuid
     * @ORM\Id
     * @ORM\Column(name="id", type="uuid", unique=true)
     */
    private $id;

    /**
     * @var Uuid
     * @ORM\Column(name="process_id", type="uuid", nullable=true)
     */
    private $processId;

    /**
     * @var Uuid
     * @ORM\Column(name="model_id", type="uuid", nullable=true)
     */
    private $modelId;

    /**
     * @var string
     * @ORM\Column(name="executable", type="string")
     */
    private $executable;

    /**
     * @var integer $numberOfValues
     *
     * @ORM\Column(name="state", type="integer")
     */
    private $state = self::STATE_IN_QUEUE;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_time_add_to_queue", type="datetime", nullable=true)
     */
    private $dateTimeAddToQueue;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_time_start", type="datetime", nullable=true)
     */
    private $dateTimeStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_time_end", type="datetime", nullable=true)
     */
    private $dateTimeEnd;

    /**
     * @var string
     *
     * @ORM\Column(name="output", type="text", nullable=true)
     */
    private $output;

    /**
     * @var string
     *
     * @ORM\Column(name="error_output", type="text", nullable=true)
     */
    private $errorOutput;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->state = self::STATE_IN_QUEUE;
        $this->dateTimeAddToQueue = new \DateTime();
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Uuid
     */
    public function getProcessId(): Uuid
    {
        return $this->processId;
    }

    /**
     * @param Uuid $processId
     * @return ModflowCalculation
     */
    public function setProcessId(Uuid $processId): ModflowCalculation
    {
        $this->processId = $processId;
        return $this;
    }

    /**
     * @return Uuid
     */
    public function getModelId(): Uuid
    {
        return $this->modelId;
    }

    /**
     * @param Uuid $modelId
     * @return ModflowCalculation
     */
    public function setModelId(Uuid $modelId): ModflowCalculation
    {
        $this->modelId = $modelId;
        return $this;
    }

    /**
     * @return string
     */
    public function getExecutable(): string
    {
        return $this->executable;
    }

    /**
     * @param string $executable
     * @return ModflowCalculation
     */
    public function setExecutable(string $executable): ModflowCalculation
    {
        $this->executable = $executable;
        return $this;
    }

    /**
     * @return int
     */
    public function getState(): int
    {
        return $this->state;
    }

    /**
     * @param int $state
     * @return ModflowCalculation
     */
    public function setState(int $state): ModflowCalculation
    {
        $this->state = $state;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateTimeAddToQueue(): \DateTime
    {
        return $this->dateTimeAddToQueue;
    }

    /**
     * @param \DateTime $dateTimeAddToQueue
     * @return ModflowCalculation
     */
    public function setDateTimeAddToQueue(\DateTime $dateTimeAddToQueue): ModflowCalculation
    {
        $this->dateTimeAddToQueue = $dateTimeAddToQueue;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateTimeStart(): \DateTime
    {
        return $this->dateTimeStart;
    }

    /**
     * @param \DateTime $dateTimeStart
     * @return ModflowCalculation
     */
    public function setDateTimeStart(\DateTime $dateTimeStart): ModflowCalculation
    {
        $this->dateTimeStart = $dateTimeStart;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getDateTimeEnd(): \DateTime
    {
        return $this->dateTimeEnd;
    }

    /**
     * @param \DateTime $dateTimeEnd
     * @return ModflowCalculation
     */
    public function setDateTimeEnd(\DateTime $dateTimeEnd): ModflowCalculation
    {
        $this->dateTimeEnd = $dateTimeEnd;
        return $this;
    }

    /**
     * @return string
     */
    public function getOutput(): string
    {
        return $this->output;
    }

    /**
     * @param string $output
     * @return ModflowCalculation
     */
    public function setOutput(string $output): ModflowCalculation
    {
        $this->output = $output;
        return $this;
    }

    /**
     * @return string
     */
    public function getErrorOutput(): string
    {
        return $this->errorOutput;
    }

    /**
     * @param string $errorOutput
     * @return ModflowCalculation
     */
    public function setErrorOutput(string $errorOutput): ModflowCalculation
    {
        $this->errorOutput = $errorOutput;
        return $this;
    }
}
