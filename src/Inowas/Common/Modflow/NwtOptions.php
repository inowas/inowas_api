<?php
/**
 * options : string
 * SPECIFIED indicates that the optional solver input values listed for items 1
 * and 2 will be specified in the NWT input file by the user.
 * SIMPLE indicates that default solver input values will be defined that work
 * well for nearly linear models. This would be used for models that do not
 * include nonlinear stress packages, and models that are either confined or
 * consist of a single unconfined layer that is thick enough to contain the
 * water table within a single layer.
 * MODERATE indicates that default solver input values will be defined that work
 * well for moderately nonlinear models. This would be used for models that include
 * nonlinear stress packages, and models that consist of one or more unconfined
 * layers. The MODERATE option should be used when the SIMPLE option does not
 * result in successful convergence.
 * COMPLEX indicates that default solver input values will be defined that work
 * well for highly nonlinear models. This would be used for models that include
 * nonlinear stress packages, and models that consist of one or more unconfined
 * layers representing complex geology and sw/gw interaction. The COMPLEX option
 * should be used when the MODERATE option does not result in successful
 * convergence.
 * (default is COMPLEX).
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class NwtOptions
{
    /** @var string */
    private $value;

    public static function fromString(string $value): NwtOptions
    {
        return new self($value);
    }

    private function __construct($value)
    {
        $this->value = $value;
    }

    public function toString(): string
    {
        return $this->value;
    }
}
