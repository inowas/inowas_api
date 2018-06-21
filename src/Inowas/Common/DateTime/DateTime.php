<?php

declare(strict_types=1);

namespace Inowas\Common\DateTime;

use DateTimeZone;

class DateTime
{

    /** @var  \DateTimeImmutable */
    protected $dateTime;

    public static function fromDateTime(\DateTime $dateTime): DateTime
    {
        $dateTimeImmutable = \DateTimeImmutable::createFromMutable($dateTime);
        return new self($dateTimeImmutable);
    }

    public static function fromDateTimeImmutable(\DateTimeImmutable $dateTimeImmutable): DateTime
    {
        return new self($dateTimeImmutable);
    }

    /**
     * @param string $dateTimeAtom
     * @return DateTime
     * @throws \Exception
     */
    public static function fromAtom(string $dateTimeAtom): DateTime
    {
        return new self(new \DateTimeImmutable($dateTimeAtom));
    }

    /**
     * @param string $dateTimeString
     * @return DateTime
     * @throws \Exception
     */
    public static function fromString(string $dateTimeString): DateTime
    {
        $dateTimeImmutable = new \DateTimeImmutable($dateTimeString, new DateTimeZone('UTC'));
        return new self($dateTimeImmutable);
    }

    public function toAtom(): string
    {
        return date_format($this->dateTime, DATE_ATOM);
    }

    public function diff(DateTime $dateTime): \DateInterval
    {
        return $this->dateTime->diff($dateTime->toDateTimeImmutable());
    }

    public function toDateTime(): \DateTime
    {
        $dateTime = new \DateTime();
        return $dateTime->setTimestamp($this->dateTime->getTimestamp());
    }

    public function toDateTimeImmutable(): \DateTimeImmutable
    {
        return $this->dateTime;
    }

    public function toFormat(string $format): string
    {
        return $this->dateTime->format($format);
    }

    private function __construct(\DateTimeImmutable $immutable)
    {
        $this->dateTime = $immutable;
    }

    public function greaterOrEqualThen(DateTime $object): bool
    {
        return ($this->toAtom() >= $object->toAtom());
    }

    public function smallerOrEqualThen(DateTime $object): bool
    {
        return ($this->toAtom() <= $object->toAtom());
    }

    public function __toString(): string
    {
        return $this->toAtom();
    }
}
