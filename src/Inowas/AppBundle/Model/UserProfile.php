<?php

namespace Inowas\AppBundle\Model;

class UserProfile
{
    /** @var string */
    private $firstName = '';

    /** @var string */
    private $lastName = '';

    /** @var string */
    private $institution = '';

    public static function create(): UserProfile
    {
        return new self();
    }

    public static function fromArray(array $arr): UserProfile
    {
        return self::readPropertiesFromArray($arr);
    }

    private function __construct()
    {}

    public function firstName(): string
    {
        return $this->firstName;
    }

    public function lastName(): string
    {
        return $this->lastName;
    }

    public function institution(): string
    {
        return $this->institution;
    }

    public function toArray(): array
    {
        return get_object_vars($this);
    }

    /**
     * @param array $arr
     * @return UserProfile
     */
    private static function readPropertiesFromArray(array $arr): UserProfile
    {
        $self = new self();
        $objectVars = get_object_vars($self);
        if (! \is_array($objectVars)) {
            return $self;
        }

        /** @var array $properties */
        $properties = $objectVars;

        /** @var string $property */
        foreach ($properties as $property => $value) {
            if (array_key_exists($property, $arr)) {
                $self->$property = $arr[$property];
            }
        }

        return $self;
    }
}
