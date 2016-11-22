<?php

namespace Inowas\ModflowBundle\Model;

use Inowas\ModflowBundle\Exception\InvalidArgumentException;

class TimeUnit
{

    const SECOND = 0;
    const MINUTE = 1;
    const DAY = 2;
    const YEAR = 3;

    /** @var integer */
    private $unit = 2;

    final private function __construct(){}

    /**
     * @param string $unit
     * @return TimeUnit
     */
    public static function fromString(string $unit){
        $instance = new self;

        switch ($unit){
            case 'day':
                $instance->unit = self::DAY;
                break;
            case 'd':
                $instance->unit = self::DAY;
                break;
            case 'minute':
                $instance->unit = self::MINUTE;
                break;
            case 'min':
                $instance->unit = self::MINUTE;
                break;
            case 'second':
                $instance->unit = self::SECOND;
                break;
            case 'sec':
                $instance->unit = self::SECOND;
                break;
            case 'year':
                $instance->unit = self::YEAR;
                break;
            case 'y':
                $instance->unit = self::YEAR;
                break;
            default:
                throw new InvalidArgumentException(sprintf('String unit %s in not matching internal unit.', $unit));
        }

        return $instance;
    }

    /**
     * @param int $value
     * @return TimeUnit
     */
    public static function fromNative(int $value){
        $instance = new self;
        $instance->unit = $value;
        return $instance;
    }

    public function toString(){
        switch ($this->unit){
            case 0:
                return 'second';
                break;
            case 1:
                return 'minute';
                break;
            case 2:
                return 'day';
                break;
            case 3:
                return 'year';
                break;
        }

        throw new InvalidArgumentException(sprintf('Internal unit %s in not matching output-string.', $this->unit));
    }

    /**
     * @return int
     */
    public function toNative(){
        return $this->unit;
    }
}