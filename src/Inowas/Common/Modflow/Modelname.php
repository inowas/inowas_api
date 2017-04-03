<?php

declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Modelname
{
    /** @var  string */
    private $name;

    public static function fromString(string $name): Modelname
    {
        return new self($name);
    }

    private function __construct(string $name)
    {
        $this->name = $name;
    }

    public function toString(): string
    {
        return $this->name;
    }

    public function slugified(): string
    {
        // replace non letter or digits by -
        $name = preg_replace('~[^\pL\d]+~u', '-', $this->name);

        // transliterate
        $name = iconv('utf-8', 'us-ascii//TRANSLIT', $name);

        // remove unwanted characters
        $name = preg_replace('~[^-\w]+~', '', $name);

        // trim
        $name = trim($name, '-');

        // remove duplicate -
        $name = preg_replace('~-+~', '-', $name);

        // lowercase
        $name = strtolower($name);

        if (empty($name)) {
            return 'n-a';
        }

        return $name;
    }
}
