<?php

declare(strict_types=1);

namespace Inowas\Common\Modflow;

/**
 * Class FileFormat
 * @package Inowas\Common\Modflow
 *
 * Format of the output file (default is 'unformatted')
 */
class FileFormat
{

    /** @var string $fileFormat */
    protected $fileFormat = 'unformatted';

    /**
     * @param string $fileFormat
     * @return FileFormat
     */
    public static function fromString(string $fileFormat): FileFormat
    {
        $self = new self();
        $self->fileFormat = $fileFormat;
        return $self;
    }

    /**
     * @param $fileFormat
     * @return FileFormat
     */
    public static function fromValue($fileFormat): FileFormat
    {
        $self = new self();
        $self->fileFormat = $fileFormat;
        return $self;
    }

    /**
     * FileFormat constructor.
     */
    private function __construct(){}

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->fileFormat;
    }

    /**
     * @return string
     */
    public function toValue(): string
    {
        return $this->fileFormat;
    }

    /**
     * @param $obj
     * @return bool
     */
    public function sameAs($obj): bool
    {
        return $obj instanceof self && $obj->toValue() === $this->fileFormat;
    }
}
