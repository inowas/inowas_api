<?php

namespace Inowas\FlopyBundle\Model\Package;

class PcgPackage implements PackageInterface
{

    /**
     * mxiter : int
     * maximum number of outer iterations. (default is 50)
     *
     * @var int
     */
    private $mxiter = 50;

    /**
     * iter1 : int
     * maximum number of inner iterations. (default is 30)
     *
     * @var int
     */
    private $iter1 = 30;

    /**
     * npcond : int
     * flag used to select the matrix conditioning method. (default is 1).
     * specify npcond = 1 for Modified Incomplete Cholesky.
     * specify npcond = 2 for Polynomial.
     *
     * @var int
     */
    private $npcond = 1;

    /**
     * hclose : float
     * is the head change criterion for convergence. (default is 1e-5).
     *
     * @var float
     */
    private $hclose = 1e-5;

    /**
     * rclose : float
     * is the residual criterion for convergence. (default is 1e-5)
     *
     * @var float
     */
    private $rclose = 1e-5;

    /**
     * relax : float
     * is the relaxation parameter used with npcond = 1. (default is 1.0)
     *
     * @var float
     */
    private $relax = 1.0;

    /**
     * nbpol : int
     * is only used when npcond = 2 to indicate whether the estimate of the
     * upper bound on the maximum eigenvalue is 2.0, or whether the estimate
     * will be calculated. nbpol = 2 is used to specify the value is 2.0;
     * for any other value of nbpol, the estimate is calculated. Convergence
     * is generally insensitive to this parameter. (default is 0).
     *
     * @var int
     */
    private $nbpol = 0;

    /**
     * iprpcg : int
     * solver print out interval. (default is 0).
     *
     * @var int
     */
    private $iprpcg = 0;

    /**
     * mutpcg : int
     * If mutpcg = 0, tables of maximum head change and residual will be
     * printed each iteration.
     * If mutpcg = 1, only the total number of iterations will be printed.
     * If mutpcg = 2, no information will be printed.
     * If mutpcg = 3, information will only be printed if convergence fails.
     * (default is 3).
     *
     * @var int
     */
    private $mutpcg = 3;

    /**
     * damp : float
     * is the steady-state damping factor. (default is 1.)
     *
     * @var float
     */
    private $damp = 1.0;

    /**
     * dampt : float
     * is the transient damping factor. (default is 1.)
     *
     * @var float
     */
    private $dampt = 1.0;

    /**
     * ihcofadd : int
     * is a flag that determines what happens to an active cell that is
     * surrounded by dry cells.  (default is 0).
     * If ihcofadd=0, cell converts to dry regardless of HCOF value.
     * This is the default, which is the way PCG2 worked prior to the
     * addition of this option. If ihcofadd<>0, cell converts to dry
     * only if HCOF has no head-dependent stresses or storage terms.
     *
     * @var int
     */
    private $ihcofadd = 0;

    /**
     * extension : list string
     * Filename extension (default is 'pcg')
     *
     * @var string
     */
    private $extension = 'pcg';

    /**
     * unitnumber : int
     * File unit number (default is 27).
     *
     * @var int
     */
    private $unitnumber = 27;

    /**
     * @param int $mxiter
     * @return PcgPackage
     */
    public function setMxiter(int $mxiter): PcgPackage
    {
        $this->mxiter = $mxiter;
        return $this;
    }

    /**
     * @param int $iter1
     * @return PcgPackage
     */
    public function setIter1(int $iter1): PcgPackage
    {
        $this->iter1 = $iter1;
        return $this;
    }

    /**
     * @param int $npcond
     * @return PcgPackage
     */
    public function setNpcond(int $npcond): PcgPackage
    {
        $this->npcond = $npcond;
        return $this;
    }

    /**
     * @param float $hclose
     * @return PcgPackage
     */
    public function setHclose(float $hclose): PcgPackage
    {
        $this->hclose = $hclose;
        return $this;
    }

    /**
     * @param float $rclose
     * @return PcgPackage
     */
    public function setRclose(float $rclose): PcgPackage
    {
        $this->rclose = $rclose;
        return $this;
    }

    /**
     * @param float $relax
     * @return PcgPackage
     */
    public function setRelax(float $relax): PcgPackage
    {
        $this->relax = $relax;
        return $this;
    }

    /**
     * @param int $nbpol
     * @return PcgPackage
     */
    public function setNbpol(int $nbpol): PcgPackage
    {
        $this->nbpol = $nbpol;
        return $this;
    }

    /**
     * @param int $iprpcg
     * @return PcgPackage
     */
    public function setIprpcg(int $iprpcg): PcgPackage
    {
        $this->iprpcg = $iprpcg;
        return $this;
    }

    /**
     * @param int $mutpcg
     * @return PcgPackage
     */
    public function setMutpcg(int $mutpcg): PcgPackage
    {
        $this->mutpcg = $mutpcg;
        return $this;
    }

    /**
     * @param float $damp
     * @return PcgPackage
     */
    public function setDamp(float $damp): PcgPackage
    {
        $this->damp = $damp;
        return $this;
    }

    /**
     * @param float $dampt
     * @return PcgPackage
     */
    public function setDampt(float $dampt): PcgPackage
    {
        $this->dampt = $dampt;
        return $this;
    }

    /**
     * @param int $ihcofadd
     * @return PcgPackage
     */
    public function setIhcofadd(int $ihcofadd): PcgPackage
    {
        $this->ihcofadd = $ihcofadd;
        return $this;
    }

    /**
     * @param string $extension
     * @return PcgPackage
     */
    public function setExtension(string $extension): PcgPackage
    {
        $this->extension = $extension;
        return $this;
    }

    /**
     * @param int $unitnumber
     * @return PcgPackage
     */
    public function setUnitnumber(int $unitnumber): PcgPackage
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
            'mxiter' => $this->mxiter,
            'iter1' => $this->iter1,
            'npcond' => $this->npcond,
            'hclose' => $this->hclose,
            'rclose' => $this->rclose,
            'relax' => $this->relax,
            'nbpol' => $this->nbpol,
            'iprpcg' => $this->iprpcg,
            'mutpcg' => $this->mutpcg,
            'damp' => $this->damp,
            'dampt' => $this->dampt,
            'ihcofadd' => $this->ihcofadd,
            'extension' => $this->extension,
            'unitnumber' => $this->unitnumber
        );
    }
}
