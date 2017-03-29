<?php
/**
 * IBOUND—is the boundary variable. One value is read for every model cell.
 * Usually, these values are read a layer at a time; however, when the XSECTION
 * option is specified, a single variable for the cross section is read.
 * Note that although IBOUND is read as one or more two-dimensional variables,
 * it is stored internally as a three-dimensional variable.
 *
 * If IBOUND(J,I,K) < 0, cell J,I,K has a constant head.
 * If IBOUND(J,I,K) = 0, cell J,I,K is inactive.
 * If IBOUND(J,I,K) > 0, cell J,I,K is active.
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class IBound
{
    
    /** @var  array */
    protected $ibound;

    public static function from3DArray(array $ibound): IBound
    {
        $self = new self();
        $self->ibound = $ibound;
        return $self;
    }

    public static function fromValue($ibound): IBound
    {
        $self = new self();
        $self->ibound = $ibound;
        return $self;
    }

    private function __construct(){}

    public function toValue()
    {
        return $this->ibound;
    }
}
