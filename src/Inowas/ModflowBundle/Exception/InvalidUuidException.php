<?php

namespace Inowas\ModflowBundle\Exception;


class InvalidUuidException extends \InvalidArgumentException
{
    public static function withId(string $id){
        return new self(sprintf("The given id %s is not a valid Uuid.", $id), 422);
    }
}