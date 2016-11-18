<?php

namespace Inowas\FlopyBundle\Model\Adapter;

use Inowas\ModflowBundle\Model\ModflowModel;

class MfPackageAdapter
{

    /** @var  ModFlowModel */
    protected $model;

    /**
     * MfPackageAdapter constructor.
     * @param ModFlowModel $model
     */
    public function __construct(ModflowModel $model){
        $this->model = $model;
    }

    /**
     * @return string
     */
    public function getModelname(): string
    {
        return $this->sanitize($this->model->getName());
    }

    /**
     * @return string
     */
    public function getNamefileExt(): string
    {
        return 'nam';
    }

    /**
     * @return string
     */
    public function getVersion(): string
    {
        return 'mf2005';
    }

    /**
     * @return string
     */
    public function getExeName(): string
    {
        return 'mf2005';
    }

    /**
     * @return boolean
     */
    public function isStructured(): bool
    {
        return true;
    }

    /**
     * @return int
     */
    public function getListunit(): int
    {
        return 2;
    }

    /**
     * @return string
     */
    public function getModelWs(): string
    {
        return 'ascii';
    }

    /**
     * @return null
     */
    public function getExternalPath()
    {
        return null;
    }

    /**
     * @return boolean
     */
    public function isVerbose(): bool
    {
        return false;
    }

    /**
     * @return boolean
     */
    public function isLoad(): bool
    {
        return true;
    }

    /**
     * @return int
     */
    public function getSilent(): int
    {
        return 0;
    }

    /**
     * @param string $str
     * @return string
     */
    private function sanitize(string $str): string {
        $str = strip_tags($str);
        $str = preg_replace('/[\r\n\t ]+/', ' ', $str);
        $str = preg_replace('/[\"\*\/\:\<\>\?\'\|]+/', ' ', $str);
        $str = strtolower($str);
        $str = html_entity_decode( $str, ENT_QUOTES, "utf-8" );
        $str = htmlentities($str, ENT_QUOTES, "utf-8");
        $str = preg_replace("/(&)([a-z])([a-z]+;)/i", '$2', $str);
        $str = str_replace(' ', '-', $str);
        $str = rawurlencode($str);
        $str = str_replace('%', '-', $str);
        return $str;
    }
}
