<?php

declare(strict_types=1);

namespace Inowas\Common\Boundaries;

class RiverStage implements \JsonSerializable
{
    /** @var float */
    private $stage;

    /** @var float */
    private $botm;

    /** @var float */
    private $cond;

    /** @var  \DateTimeImmutable */
    private $dateTime;

    public static function fromDateTimeStageBotCond(\DateTimeImmutable $dateTime, float $stage, float $botm, float $cond): RiverStage
    {
        $self = new self();
        $self->stage = $stage;
        $self->botm = $botm;
        $self->cond = $cond;
        $self->dateTime = $dateTime;

        return $self;
    }
}
