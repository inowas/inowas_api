<?php

declare(strict_types=1);

namespace Inowas\Common\Modflow;

/**
 * Class FileHeader
 * @package Inowas\Common\Modflow
 *
 * Header for the output file (default is 'extended')
 */
class FileHeader
{

    /** @var string $fileHeader */
    protected $fileHeader = 'extended';

    /**
     * @param string $fileHeader
     * @return FileHeader
     */
    public static function fromString(string $fileHeader): FileHeader
    {
        $self = new self();
        $self->fileHeader = $fileHeader;
        return $self;
    }

    /**
     * @param $fileHeader
     * @return FileHeader
     */
    public static function fromValue($fileHeader): FileHeader
    {
        $self = new self();
        $self->fileHeader = $fileHeader;
        return $self;
    }

    /**
     * FileHeader constructor.
     */
    private function __construct(){}

    /**
     * @return string
     */
    public function toString(): string
    {
        return $this->fileHeader;
    }

    /**
     * @return string
     */
    public function toValue(): string
    {
        return $this->fileHeader;
    }

    /**
     * @param $obj
     * @return bool
     */
    public function sameAs($obj): bool
    {
        return $obj instanceof self && $obj->toValue() === $this->fileHeader;
    }
}
