<?php

declare(strict_types=1);

namespace Inowas\ModflowCalculation\Model;

use Inowas\Common\DateTime\DateTime;
use Inowas\Common\Id\ModflowId;
use Inowas\Common\Id\UserId;
use Inowas\Common\Modflow\LengthUnit;
use Inowas\Common\Modflow\PackageName;
use Inowas\Common\Modflow\StressPeriods;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\ModflowCalculation\Model\Event\CalculationFlowPackageWasChanged;
use Inowas\ModflowCalculation\Model\Event\CalculationPackageParameterWasUpdated;
use Inowas\ModflowCalculation\Model\Event\CalculationStressperiodsWereUpdated;
use Inowas\ModflowCalculation\Model\Event\CalculationWasCloned;
use Inowas\ModflowCalculation\Model\Event\CalculationWasFinished;
use Inowas\ModflowCalculation\Model\Event\CalculationWasQueued;
use Inowas\ModflowCalculation\Model\Event\CalculationWasStarted;
use Inowas\ModflowCalculation\Model\Event\EndDateTimeWasUpdated;
use Inowas\ModflowCalculation\Model\Event\CalculationWasCreated;
use Inowas\ModflowCalculation\Model\Event\LengthUnitWasUpdated;
use Inowas\ModflowCalculation\Model\Event\StartDateTimeWasUpdated;
use Inowas\ModflowCalculation\Model\Event\TimeUnitWasUpdated;
use Prooph\EventSourcing\AggregateRoot;

class ModflowCalculationAggregate extends AggregateRoot
{
    /** @var ModflowId */
    private $calculationId;

    /** @var ModflowId */
    private $modflowModelId;

    /** @var  UserId */
    private $ownerId;

    /** @var  TimeUnit */
    private $timeUnit;

    /** @var  LengthUnit */
    private $lengthUnit;

    /** @var  DateTime */
    private $startDateTime;

    /** @var  DateTime */
    private $endDateTime;

    /** @var  StressPeriods */
    private $stressPeriods;

    /** @var ModflowCalculationConfiguration */
    private $configuration;

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param ModflowId $calculationId
     * @param ModflowId $modflowModelId
     * @param UserId $userId
     * @param DateTime $start
     * @param DateTime $end
     * @param LengthUnit $lengthUnit
     * @param TimeUnit $timeUnit
     * @param StressPeriods $stressPeriods
     * @return ModflowCalculationAggregate
     */
    public static function create(
        ModflowId $calculationId,
        ModflowId $modflowModelId,
        UserId $userId,
        DateTime $start,
        DateTime $end,
        LengthUnit $lengthUnit,
        TimeUnit $timeUnit,
        StressPeriods $stressPeriods
    ): ModflowCalculationAggregate
    {
        $self = new self();
        $self->calculationId = $calculationId;
        $self->modflowModelId = $modflowModelId;
        $self->ownerId = $userId;
        $self->startDateTime = $start;
        $self->endDateTime = $end;
        $self->lengthUnit = $lengthUnit;
        $self->timeUnit = $timeUnit;
        $self->stressPeriods = $stressPeriods;
        $self->configuration = ModflowCalculationConfiguration::createFromDefaultsWithId($calculationId);

        $self->recordThat(
            CalculationWasCreated::fromModelWithProps(
                $userId,
                $calculationId,
                $modflowModelId,
                $start,
                $end,
                $lengthUnit,
                $timeUnit,
                $stressPeriods
            )
        );

        return $self;
    }

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param ModflowId $newCalculationId
     * @param ModflowId $newModelId
     * @param UserId $userId
     * @param ModflowCalculationAggregate $calculation
     * @return ModflowCalculationAggregate
     */
    public static function clone(ModflowId $newCalculationId, ModflowId $newModelId, UserId $userId, ModflowCalculationAggregate $calculation): ModflowCalculationAggregate
    {
        $self = new self();
        $self->calculationId = $newCalculationId;
        $self->modflowModelId = $newModelId;
        $self->ownerId = $userId;
        $self->startDateTime = $calculation->startDateTime();
        $self->endDateTime = $calculation->endDateTime();
        $self->lengthUnit = $calculation->lengthUnit();
        $self->timeUnit = $calculation->timeUnit();
        $self->stressPeriods = $calculation->stressPeriods();
        $self->configuration = $calculation->configuration;

        $self->recordThat(
            CalculationWasCloned::byUserWithIds(
                $self->ownerId,
                $calculation->calculationId(),
                $self->calculationId,
                $self->modflowModelId,
                $self->startDateTime,
                $self->endDateTime,
                $self->lengthUnit,
                $self->timeUnit,
                $self->stressPeriods
            )
        );

        return $self;
    }

    public function updateStartDateTime(DateTime $start): void
    {
        $this->startDateTime = $start;
        $this->recordThat(StartDateTimeWasUpdated::to($this->calculationId, $start));
    }

    public function updateEndDateTime(DateTime $end): void
    {
        $this->endDateTime = $end;
        $this->recordThat(EndDateTimeWasUpdated::to($this->calculationId, $end));
    }

    public function updateLengthUnit(LengthUnit $lengthUnit): void
    {
        if (! $this->lengthUnit->sameAs($lengthUnit)){
            $this->lengthUnit = $lengthUnit;
            $this->recordThat(LengthUnitWasUpdated::to($this->calculationId, $lengthUnit));
        }
    }

    public function updateTimeUnit(TimeUnit $timeUnit): void
    {
        if (! $this->timeUnit->sameAs($timeUnit)){
            $this->timeUnit = $timeUnit;
            $this->recordThat(TimeUnitWasUpdated::to($this->calculationId, $timeUnit));
        }
    }

    public function updateStressperiods(UserId $userId, StressPeriods $stressPeriods): void
    {
        $this->stressPeriods = $stressPeriods;
        $this->recordThat(CalculationStressperiodsWereUpdated::withProps($userId, $this->calculationId, $stressPeriods));
    }

    public function changeFlowPackage(UserId $userId, PackageName $packageName): void
    {
        if ($this->configuration->flowPackageName() !== $packageName->toString()){
            $this->configuration->changeFlowPackage($packageName);

            $this->recordThat(CalculationFlowPackageWasChanged::to(
                $userId,
                $this->calculationId,
                $packageName
            ));
        }
    }

    /** @noinspection MoreThanThreeArgumentsInspection
     * @param UserId $userId
     * @param string $packageName
     * @param string $parameterName
     * @param $parameterData
     */
    public function updatePackageParameter(UserId $userId, string $packageName, string $parameterName, $parameterData): void
    {
        if ($this->configuration->canUpdatePackageParameter($packageName, $parameterName)){
            $this->recordThat(CalculationPackageParameterWasUpdated::withProps(
                $userId,
                $this->calculationId,
                $packageName,
                $parameterName,
                $parameterData
            ));
        }
    }

    public function calculationHasQueued(): void
    {
        $this->recordThat(CalculationWasQueued::withId($this->calculationId));
    }

    public function calculationHasStarted(): void
    {
        $this->recordThat(CalculationWasStarted::withId($this->calculationId));
    }

    public function calculationHasFinished(ModflowCalculationResponse $response): void
    {
        $this->recordThat(CalculationWasFinished::withIdAndResponse($this->calculationId, $response));
    }

    public function calculationId(): ModflowId
    {
        return $this->calculationId;
    }

    public function modelId(): ModflowId
    {
        return $this->modflowModelId;
    }

    public function ownerId(): UserId
    {
        return $this->ownerId;
    }

    public function startDateTime(): DateTime
    {
        return $this->startDateTime;
    }

    public function endDateTime(): DateTime
    {
        return $this->endDateTime;
    }

    public function lengthUnit(): LengthUnit
    {
        return $this->lengthUnit;
    }

    public function timeUnit(): TimeUnit
    {
        return $this->timeUnit;
    }

    public function stressPeriods(): StressPeriods
    {
        return $this->stressPeriods;
    }

    public function packages(): ModflowCalculationConfiguration
    {
        return $this->configuration;
    }

    protected function whenCalculationPackageParameterWasUpdated(CalculationPackageParameterWasUpdated $event): void
    {}

    protected function whenCalculationFlowPackageWasChanged(CalculationFlowPackageWasChanged $event): void
    {
        $this->configuration->changeFlowPackage($event->packageName());
    }

    protected function whenCalculationWasCreated(CalculationWasCreated $event): void
    {
        $this->calculationId = $event->calculationId();
        $this->modflowModelId = $event->modflowmodelId();
        $this->ownerId = $event->userId();
        $this->startDateTime = $event->start();
        $this->endDateTime = $event->end();
        $this->lengthUnit = $event->lengthUnit();
        $this->timeUnit = $event->timeUnit();
        $this->stressPeriods = $event->stressPeriods();
        $this->configuration = ModflowCalculationConfiguration::createFromDefaultsWithId($event->calculationId());
    }

    protected function whenCalculationWasCloned(CalculationWasCloned $event): void
    {
        $this->calculationId = $event->calculationId();
        $this->modflowModelId = $event->modflowmodelId();
        $this->ownerId = $event->userId();
        $this->startDateTime = $event->start();
        $this->endDateTime = $event->end();
        $this->lengthUnit = $event->lengthUnit();
        $this->timeUnit = $event->timeUnit();
        $this->stressPeriods = $event->stressPeriods();
        $this->configuration = ModflowCalculationConfiguration::createFromDefaultsWithId($event->calculationId());
    }

    protected function whenCalculationStressperiodsWereUpdated(CalculationStressperiodsWereUpdated $event): void
    {
        $this->stressPeriods = $event->stressPeriods();
    }

    protected function whenCalculationWasQueued(CalculationWasQueued $event): void
    {}

    protected function whenCalculationWasStarted(CalculationWasStarted $event): void
    {}

    protected function whenCalculationWasFinished(CalculationWasFinished $event): void
    {}

    protected function whenLengthUnitWasUpdated(LengthUnitWasUpdated $event): void
    {
        $this->lengthUnit = $event->lengthUnit();
        $this->configuration->updateLengthUnit($event->lengthUnit());
    }

    protected function whenTimeUnitWasUpdated(TimeUnitWasUpdated $event): void
    {
        $this->timeUnit = $event->timeUnit();
        $this->configuration->updateTimeUnit($event->timeUnit());
    }

    protected function whenStartDateTimeWasUpdated(StartDateTimeWasUpdated $event): void
    {
        $this->startDateTime = $event->start();
        $this->configuration->updateStartDateTime($event->start());
    }

    protected function whenEndDateTimeWasUpdated(EndDateTimeWasUpdated $event): void
    {
        $this->endDateTime = $event->end();
    }

    protected function aggregateId(): string
    {
        return $this->calculationId->toString();
    }
}
