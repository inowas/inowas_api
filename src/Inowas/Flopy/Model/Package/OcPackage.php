<?php

namespace Inowas\FlopyBundle\Model\Package;

use Inowas\FlopyBundle\Model\ValueObject\StressPeriodOutputControl;

class OcPackage implements PackageInterface
{
    /**
     * ihedfm : int
     * is a code for the format in which heads will be printed.
     * (default is 0).
     *
     * @var int
     */
    private $ihedfm = 0;

    /**
     * iddnfm : int
     * is a code for the format in which heads will be printed.
     * (default is 0).
     *
     * @var int
     */
    private $iddnfm = 0;

    /**
     * chedfm : string
     * is a character value that specifies the format for saving heads.
     * The format must contain 20 characters or less and must be a valid
     * Fortran format that is enclosed in parentheses. The format must be
     * enclosed in apostrophes if it contains one or more blanks or commas.
     * The optional word LABEL after the format is used to indicate that
     * each layer of output should be preceded with a line that defines the
     * output (simulation time, the layer being output, and so forth). If
     * there is no record specifying CHEDFM, then heads are written to a
     * binary (unformatted) file. Binary files are usually more compact than
     * text files, but they are not generally transportable among different
     * computer operating systems or different Fortran compilers.
     * (default is None)
     *
     * @var string|null
     */
    private $chedfm = null;

    /**
     *  cddnfm : string
     * is a character value that specifies the format for saving drawdown.
     * The format must contain 20 characters or less and must be a valid
     * Fortran format that is enclosed in parentheses. The format must be
     * enclosed in apostrophes if it contains one or more blanks or commas.
     * The optional word LABEL after the format is used to indicate that
     * each layer of output should be preceded with a line that defines the
     * output (simulation time, the layer being output, and so forth). If
     * there is no record specifying CDDNFM, then drawdowns are written to a
     * binary (unformatted) file. Binary files are usually more compact than
     * text files, but they are not generally transportable among different
     * computer operating systems or different Fortran compilers.
     * (default is None)
     *
     * @var string|null
     */
    private $cddnfm = null;

    /**
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
     *
     * @var string|null
     */
    private $cboufm = null;

    /**
     * compact : boolean
     * Save results in compact budget form. (default is True).
     *
     * @var bool
     */
    private $compact = true;

    /**
     * stress_period_data : dictionary of of lists
     * Dictionary key is a tuple with the zero-based period and step
     * (IPEROC, ITSOC) for each print/save option list.
     * (default is {(0,0):['save head']})
     *
     * The list can have any valid MODFLOW OC print/save option:
     * PRINT HEAD
     * PRINT DRAWDOWN
     * PRINT BUDGET
     * SAVE HEAD
     * SAVE DRAWDOWN
     * SAVE BUDGET
     * SAVE IBOUND
     *
     * The lists can also include (1) DDREFERENCE in the list to reset
     * drawdown reference to the period and step and (2) a list of layers
     * for PRINT HEAD, SAVE HEAD, PRINT DRAWDOWN, SAVE DRAWDOWN, and
     * SAVE IBOUND.
     *
     * The list is used for every stress period and time step after the
     * (IPEROC, ITSOC) tuple until a (IPEROC, ITSOC) tuple is entered with
     * and empty list.
     *
     * @var array of StressPeriodOutputControl-Objects
     */
    private $stress_period_data;

    /**
     * extension : list of strings
     * (default is ['oc','hds','ddn','cbc']).
     *
     * @var array
     */
    private $extension = ['oc', 'hds', 'ddn', 'cbc'];

    /**
     * unitnumber : list of ints
     * (default is [14, 51, 52, 53]).
     *
     * @var array
     */
    private $unitnumber = [14, 51, 52, 53];

    public function __construct()
    {
        $this->stress_period_data = array();
        $this->stress_period_data[] = StressPeriodOutputControl::create(0, 0, StressPeriodOutputControl::SAVE_HEAD);
    }

    /**
     * @param int $ihedfm
     * @return OcPackage
     */
    public function setIhedfm(int $ihedfm): OcPackage
    {
        $this->ihedfm = $ihedfm;
        return $this;
    }

    /**
     * @param int $iddnfm
     * @return OcPackage
     */
    public function setIddnfm(int $iddnfm): OcPackage
    {
        $this->iddnfm = $iddnfm;
        return $this;
    }

    /**
     * @param null|string $chedfm
     * @return OcPackage
     */
    public function setChedfm($chedfm)
    {
        $this->chedfm = $chedfm;
        return $this;
    }

    /**
     * @param null|string $cddnfm
     * @return OcPackage
     */
    public function setCddnfm($cddnfm)
    {
        $this->cddnfm = $cddnfm;
        return $this;
    }

    /**
     * @param null|string $cboufm
     * @return OcPackage
     */
    public function setCboufm($cboufm)
    {
        $this->cboufm = $cboufm;
        return $this;
    }

    /**
     * @param boolean $compact
     * @return OcPackage
     */
    public function setCompact(bool $compact): OcPackage
    {
        $this->compact = $compact;
        return $this;
    }

    /**
     * @param array $stress_period_data
     * @return OcPackage
     */
    public function setStressPeriodData(array $stress_period_data): OcPackage
    {
        $this->stress_period_data = $stress_period_data;
        return $this;
    }

    /**
     * @param array $extension
     * @return OcPackage
     */
    public function setExtension(array $extension): OcPackage
    {
        $this->extension = $extension;
        return $this;
    }

    /**
     * @param array $unitnumber
     * @return OcPackage
     */
    public function setUnitnumber(array $unitnumber): OcPackage
    {
        $this->unitnumber = $unitnumber;
        return $this;
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return array(
            'ihedfm' => $this->ihedfm,
            'iddnfm' => $this->iddnfm,
            'chedfm' => $this->chedfm,
            'cddnfm' => $this->cddnfm,
            'cboufm' => $this->cboufm,
            'compact' => $this->compact,
            'stress_period_data' => $this->stress_period_data,
            'extension' => $this->extension,
            'unitnumber' => $this->unitnumber
        );
    }
}
