<?php

declare(strict_types=1);

namespace Inowas\Common\DateTime;

class DateTime
{

    /** @var  \DateTimeImmutable */
    protected $dateTime;

    public static function fromDateTime(\DateTime $dateTime): DateTime
    {
        $dateTimeImmutable = \DateTimeImmutable::createFromMutable($dateTime);
        return new self($dateTimeImmutable);
    }

    public static function fromAtom(string $dateTimeAtom): DateTime
    {
        return new self(\DateTimeImmutable::createFromFormat(DATE_ATOM, $dateTimeAtom));
    }

    public function toAtom(): string
    {
        return date_format($this->dateTime, DATE_ATOM);
    }

    private function __construct(\DateTimeImmutable $immutable)
    {
        $this->dateTime = $immutable;
    }
}
