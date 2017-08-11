<?php

namespace Inowas\AppBundle\Model;

class UserPermission
{
    /** @var  string */
    private $permission;

    public static function noPermission(): UserPermission
    {
        return new self('---');
    }

    public static function readOnly(): UserPermission
    {
        return new self('r--');
    }

    public static function readWrite(): UserPermission
    {
        return new self('rw-');
    }

    public static function readWriteExecute(): UserPermission
    {
        return new self('rwx');
    }

    private function __construct(string $permission)
    {
        $this->permission = $permission;
    }

    public function toString(): string
    {
        return $this->permission;
    }
}
