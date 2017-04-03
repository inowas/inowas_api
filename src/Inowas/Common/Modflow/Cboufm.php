<?php
/**
 * Package ModflowOc
 *
 * cboufm : string
 * is a character value that specifies the format for saving ibound.
 * The format must contain 20 characters or less and must be a valid
 * Fortran format that is enclosed in parentheses. The format must be
 * enclosed in apostrophes if it contains one or more blanks or commas.
 * The optional word LABEL after the format is used to indicate that
 * each layer of output should be preceded with a line that defines the
 * output (simulation time, the layer being output, and so forth). If
 * there is no record specifying CBOUFM, then ibounds are written to a
 * binary (unformatted) file. Binary files are usually more compact than
 * text files, but they are not generally transportable among different
 * computer operating systems or different Fortran compilers.
 * (default is None)
 */
declare(strict_types=1);

namespace Inowas\Common\Modflow;

class Cboufm
{
    /** @var null|string */
    private $value;

    public static function fromString(string $value): Cboufm
    {
        return new self($value);
    }

    public static function fromValue($value): Cboufm
    {
        return new self($value);
    }

    private function __construct($value)
    {
        $this->value = $value;
    }

    public function toString(): ?string
    {
        return $this->value;
    }

    public function toValue()
    {
        return $this->value;
    }
}
