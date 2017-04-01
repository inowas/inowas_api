<?php

declare(strict_types=1);

namespace Inowas\Modflow\Projection\BoundaryList;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Schema\Schema;
use Inowas\Common\DateTime\DateTime;
use Inowas\Common\DateTime\TotalTime;
use Inowas\Common\Modflow\TimeUnit;
use Inowas\Common\Projection\AbstractDoctrineConnectionProjector;
use Inowas\Modflow\Model\Event\BoundaryWasAdded;
use Inowas\Modflow\Model\Event\ModflowModelWasCreated;
use Inowas\Modflow\Model\Exception\InvalidTimeUnitException;
use Inowas\Modflow\Projection\Table;

class StressPeriodsProjector extends AbstractDoctrineConnectionProjector
{

    public function __construct(Connection $connection) {

        parent::__construct($connection);

        $this->schema = new Schema();
        $table = $this->schema->createTable(Table::MODEL_STRESS_PERIODS);
        $table->addColumn('model_id', 'string', ['length' => 36]);
        $table->addColumn('start', 'text', ['notnull' => false]);
        $table->addColumn('end', 'text', ['notnull' => false]);
        $table->addColumn('length_unit', 'integer', ['notnull' => false]);
        $table->addColumn('time_unit', 'integer', ['notnull' => false]);
        $table->addColumn('stress_periods', 'text', ['notnull' => false]);
        $table->addColumn('bc_dates', 'text', ['notnull' => false]);
        $table->addColumn('nper', 'integer', ['notnull' => false]);
        $table->addColumn('perlen', 'text', ['notnull' => false]);
        $table->addColumn('nstp', 'text', ['notnull' => false]);
        $table->addColumn('tsmult', 'text', ['notnull' => false]);
        $table->addColumn('steady', 'text', ['notnull' => false]);
        $table->setPrimaryKey(['model_id']);
    }

    public function onModflowModelWasCreated(ModflowModelWasCreated $event): void
    {
        $this->connection->insert(Table::MODEL_STRESS_PERIODS, array(
            'model_id' => $event->modflowModelId()->toString(),
            'length_unit' => $event->lengthUnit()->toInt(),
            'time_unit' => $event->timeUnit()->toInt(),
            'bc_dates' => \json_encode([]),
            'stress_periods' => \json_encode([])
        ));
    }

    public function onBoundaryWasAdded(BoundaryWasAdded $event): void
    {

        // TODO
        // Adding StartDate and EndDate to the list of dates
        // Retrieve the timeUnit from the Database

        $result = $this->connection->fetchAssoc(
            sprintf('SELECT bc_dates FROM %s WHERE model_id = :model_id', Table::MODEL_STRESS_PERIODS),
            ['model_id' => $event->modflowId()->toString()]
        );

        if ($result === false){
            return;
        }

        $bcDates = \json_decode($result['bc_dates']);
        $numberOfBcDates = count($bcDates);

        /**
         * Array of DateTimeValues
         * @var array $boundaryData
         */
        $dateTimeValues = \json_decode($event->boundary()->dataToJson());
        foreach ($dateTimeValues as $dateTimeValue){
            $dateTimeAtom = DateTime::fromDateTime(new \DateTime($dateTimeValue->date_time))->toAtom();
            if (! in_array($dateTimeAtom, $bcDates)) {
                $bcDates[] = $dateTimeAtom;
            }
        }

        if (count($bcDates) === $numberOfBcDates){
            return;
        }


        sort($bcDates);

        $totims = $this->calculateTotims($bcDates, TimeUnit::fromInt(TimeUnit::DAYS));

        $perlen = [];
        $nstp = [];
        $tsmult = [];
        $steady = [];
        for ($i=1; $i < count($totims); $i++){
            $perlen[] = ($totims[$i]->toInteger())-($totims[$i-1]->toInteger());
            $nstp[] = ($totims[$i]->toInteger())-($totims[$i-1]->toInteger());
            $tsmult[] = 1;
            $steady[] = false;
        }

        $this->connection->update(
            Table::MODEL_STRESS_PERIODS,
            array(
                'bc_dates' => \json_encode($bcDates),
                'nper' => count($bcDates),
                'perlen' =>  \json_encode($perlen),
                'nstp' =>  \json_encode($nstp),
                'tsmult' =>  \json_encode($tsmult),
                'steady' =>  \json_encode($steady)
            ),
            array('model_id' => $event->modflowId()->toString())
        );
    }

    private function calculateTotims(array $bcDates, TimeUnit $timeUnit){
        $totims = [];
        $start = new \DateTime($bcDates[0]);
        foreach ($bcDates as $bcDate){
            $totims[] = $this->calculateTotim($start, new \DateTime($bcDate), $timeUnit);
        }

        return $totims;
    }

    private function calculateTotim(\DateTime $start, \DateTime $dateTime, TimeUnit $timeUnit): TotalTime
    {
        $start = clone $start;
        $dateTime = clone $dateTime;
        $dateTime->modify('+1 day');
        $diff = $start->diff($dateTime);

        if ($timeUnit->toInt() === $timeUnit::SECONDS){
            return TotalTime::fromInt($dateTime->getTimestamp() - $start->getTimestamp());
        }

        if ($timeUnit->toInt() === $timeUnit::MINUTES){
            return TotalTime::fromInt((int)(($dateTime->getTimestamp() - $start->getTimestamp())/60));
        }

        if ($timeUnit->toInt() === $timeUnit::HOURS){
            return TotalTime::fromInt((int)(($dateTime->getTimestamp() - $start->getTimestamp())/60/60));
        }

        if ($timeUnit->toInt() === $timeUnit::DAYS){
            return TotalTime::fromInt((int)$diff->format("%a"));
        }

        throw InvalidTimeUnitException::withTimeUnitAndAvailableTimeUnits($timeUnit, $timeUnit->availableTimeUnits);
    }
}
