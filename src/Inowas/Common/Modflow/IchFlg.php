<?php
/**
 * ichflg : bool, optional
 * Flag indicating that flows between constant head cells should be calculated
 * (the default is False).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class IchFlg
{
    /** @var bool */
    protected $ichflg;

    public static function fromBool(bool $ichflg): IchFlg
    {
        $self = new self();
        $self->ichflg = $ichflg;
        return $self;
    }

    public static function fromValue($ichflg): IchFlg
    {
        $self = new self();
        $self->ichflg = $ichflg;
        return $self;
    }

    private function __construct(){}

    public function toBool(): bool
    {
        return $this->ichflg;
    }

    public function toValue(): bool
    {
        return $this->ichflg;
    }
}
