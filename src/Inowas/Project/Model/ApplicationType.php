<?php

namespace Inowas\Project\Model;

final class ApplicationType
{

    const MODEL_SETUP = 'A03';
    const SCENARIOANALYSIS = 'A07';

    public static $availableTypes = array(
        'A03', 'A07'
    );

    /** @var  string */
    private $type;

    public static function isValid(ApplicationType $type): bool
    {
        return in_array($type->toString(), self::$availableTypes, true);
    }

    public static function fromString(string $type): ApplicationType
    {
        return new self($type);
    }

    private function __construct(string $type)
    {
        $this->type = $type;
    }

    public function toString(): string
    {
        return $this->type;
    }

    public function sameAs($value): bool
    {
        if (! $value instanceof ApplicationType){
            return false;
        }

        return $this->type === $value->toString();
    }
}
