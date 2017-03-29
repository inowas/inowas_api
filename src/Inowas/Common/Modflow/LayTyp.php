<?php
/**
 * laytyp : int or array of ints (nlay)
 * Layer type (default is 0).
 * 0—confined
 * not 0—convertible.(MODFLOW-2000)
 * >0 – convertible  (MODFLOW-2005 and models derived from it)
 * <0 – convertible unless the THICKSTRT option is in effect.
 * When THICKSTRT is in effect, a negative value of LAYTYP indicates that the layer is confined,
 * and its saturated thickness will be computed as STRT-BOT.
 * (MODFLOW-2005 and models derived from it only)
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;


class LayTyp
{

    const TYPE_CONFINED = 0;
    const TYPE_CONVERTIBLE = 1;
    const TYPE_CONVERTIBLE_UNLESS_THICKSTRT_OPTION_IS_IN_EFFECT = -1;

    /** @var  */
    private $type;

    private function __construct()
    {}

    public static function fromArray(array $value): LayTyp
    {
        $self = new self();
        $self->type = $value;
        return $self;
    }

    public static function fromInt(int $value): LayTyp
    {
        $self = new self();
        $self->type = $value;
        return $self;
    }

    public static function fromValue($value): LayTyp
    {
        $self = new self();
        $self->type = $value;
        return $self;
    }

    public function toArray(): array
    {
        return $this->type;
    }

    public function toInt(): int
    {
        return $this->type;
    }

    public function toValue()
    {
        return $this->type;
    }

    public function isArray(): bool
    {
        return is_array($this->type);
    }
}
