<?php

namespace Inowas\ModflowBundle\Model;

use Inowas\Flopy\Model\Package\CalculationProperties;
use Ramsey\Uuid\Uuid;

class Calculation
{

    const STATE_IN_QUEUE = 0;
    const STATE_RUNNING = 1;
    const STATE_FINISHED_SUCCESSFUL = 11;
    const STATE_FINISHED_WITH_ERRORS = 12;

    /** @var Uuid */
    private $id;

    /** @var Uuid */
    private $modelId;

    /** @var  CalculationProperties */
    private $calculationProperties;

    /** @var string */
    private $calculationUrl;

    /** @var string */
    private $modelUrl;

    /** @var string */
    private $submitHeadsUrl;

    /** @var  Uuid */
    private $apiKey;

    /** @var string */
    private $dataFolder;

    /** @var int  */
    private $state = self::STATE_IN_QUEUE;

    /** @var \DateTime */
    private $dateTimeAddToQueue;

    /** @var \DateTime */
    private $dateTimeStart;

    /** @var \DateTime */
    private $dateTimeEnd;

    /** @var string */
    private $output;

    /** @var bool */
    private $finishedWithSuccess = false;


    public function __construct(CalculationProperties $calculationProperties)
    {
        $this->id = Uuid::uuid4();
        $this->calculationProperties = $calculationProperties;
        $this->state = self::STATE_IN_QUEUE;
        $this->dateTimeAddToQueue = new \DateTime();
    }

    /**
     * @return Uuid
     */
    public function getId(): Uuid
    {
        return $this->id;
    }

    /**
     * @return CalculationProperties
     */
    public function getCalculationProperties(): CalculationProperties
    {
        return $this->calculationProperties;
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
     * @return Calculation
     */
    public function setModelId(Uuid $modelId): Calculation
    {
        $this->modelId = $modelId;
        return $this;
    }

    /**
     * @return string
     */
    public function getCalculationUrl(): string
    {
        return $this->calculationUrl;
    }

    /**
     * @param string $calculationUrl
     * @return Calculation
     */
    public function setCalculationUrl(string $calculationUrl): Calculation
    {
        $this->calculationUrl = $calculationUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getModelUrl(): string
    {
        return $this->modelUrl;
    }

    /**
     * @param string $modelUrl
     * @return Calculation
     */
    public function setModelUrl(string $modelUrl): Calculation
    {
        $this->modelUrl = $modelUrl;
        return $this;
    }

    /**
     * @return string
     */
    public function getSubmitHeadsUrl(): string
    {
        return $this->submitHeadsUrl;
    }

    /**
     * @param string $submitHeadsUrl
     * @return Calculation
     */
    public function setSubmitHeadsUrl(string $submitHeadsUrl): Calculation
    {
        $this->submitHeadsUrl = $submitHeadsUrl;
        return $this;
    }

    /**
     * @return Uuid
     */
    public function getApiKey(): Uuid
    {
        return $this->apiKey;
    }

    /**
     * @param Uuid $apiKey
     * @return Calculation
     */
    public function setApiKey(Uuid $apiKey): Calculation
    {
        $this->apiKey = $apiKey;
        return $this;
    }

    /**
     * @return string
     */
    public function getDataFolder(): string
    {
        return $this->dataFolder;
    }

    /**
     * @param string $dataFolder
     * @return Calculation
     */
    public function setDataFolder(string $dataFolder): Calculation
    {
        $this->dataFolder = $dataFolder;
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
     * @return Calculation
     */
    public function setState(int $state): Calculation
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
     * @return Calculation
     */
    public function setDateTimeAddToQueue(\DateTime $dateTimeAddToQueue): Calculation
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
     * @return Calculation
     */
    public function setDateTimeStart(\DateTime $dateTimeStart): Calculation
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
     * @return Calculation
     */
    public function setDateTimeEnd(\DateTime $dateTimeEnd): Calculation
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
     * @return Calculation
     */
    public function setOutput(string $output): Calculation
    {
        $this->output = $output;
        return $this;
    }

    /**
     * @return boolean
     */
    public function isFinishedWithSuccess(): bool
    {
        return $this->finishedWithSuccess;
    }

    /**
     * @param boolean $finishedWithSuccess
     * @return Calculation
     */
    public function setFinishedWithSuccess(bool $finishedWithSuccess): Calculation
    {
        $this->finishedWithSuccess = $finishedWithSuccess;
        return $this;
    }
}
