<?php

declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Filenames
{
    /** @var array */
    private $filenames;

    public static function fromValues($filenames): Filenames
    {
        if (\is_array($filenames)) {
            return new self($filenames);
        }

        return new self([$filenames]);
    }

    private function __construct(array $filenames)
    {
        $this->filenames = $filenames;
    }

    public function toValues()
    {
        if (\is_array($this->filenames) && \count($this->filenames) > 1) {
            return $this->filenames;
        }

        if (\is_array($this->filenames) && \count($this->filenames) === 1) {
            return $this->filenames[0];
        }

        return $this->filenames;
    }
}
