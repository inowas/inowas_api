<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model;

use Inowas\Modflow\Model\Exception\InvalidModflowVersionException;

class ModflowVersion
{

    const MF2005 = 'mf2005';

    protected $availableVersions = array('mf2005');

    /** @var  string */
    private $version;

    public static function fromString(string $version): ModflowVersion
    {
        return new self($version);
    }

    private function __construct(string $version) {
        if (! in_array($version, $this->availableVersions)){
            throw InvalidModflowVersionException::withVersion($version, $this->availableVersions);
        }

        $this->$version = $version;
    }

    public function toString(): string
    {
        return $this->version;
    }
}
