<?php

declare(strict_types=1);

namespace Inowas\Modflow\Model\Packages;

use Inowas\Common\FileSystem\ExternalPath;
use Inowas\Common\FileSystem\FileExtension;
use Inowas\Common\FileSystem\FileName;
use Inowas\Common\FileSystem\ModelWorkSpace;
use Inowas\Common\Modflow\ListUnit;
use Inowas\Common\Modflow\Verbose;
use Inowas\Modflow\Model\ModflowModelName;
use Inowas\Modflow\Model\ModflowVersion;

class MfPackage implements \JsonSerializable
{

    /** @var  ModflowModelName */
    protected $modelname;

    /** @var  FileExtension */
    protected $nameFileExtension;

    /** @var ModflowVersion */
    protected $version;

    /** @var FileName */
    protected $executableName;

    /** @var bool */
    protected $structured = true;

    /** @var ListUnit  */
    protected $listUnit;

    /** @var ModelWorkSpace */
    protected $modelWorkSpace = '.';

    /** @var ExternalPath */
    protected $externalPath = null;

    /** @var Verbose $verbose  */
    protected $verbose;

    public static function fromParams(
        ModflowModelName $name,
        ?FileExtension $fileExtension = null,
        ?ModflowVersion $version = null,
        ?FileName $executableName = null,
        ?ListUnit $listUnit = null,
        ?ModelWorkSpace $modelWorkSpace = null,
        ?ExternalPath $externalPath = null,
        ?Verbose $verbose = null
    ): MfPackage
    {
        $self = new self();
        $self->modelname = $name;
        $self->nameFileExtension = $fileExtension;
        $self->version = $version;
        $self->executableName = $executableName;
        $self->listUnit = $listUnit;
        $self->modelWorkSpace = $modelWorkSpace;
        $self->externalPath = $externalPath;
        $self->verbose = $verbose;

        if (! $self->nameFileExtension instanceof FileExtension){
            $self->nameFileExtension = FileExtension::fromString('nam');
        }

        if (! $self->version instanceof ModflowVersion) {
            $self->version = ModflowVersion::fromString(ModflowVersion::MF2005);
        }

        if (! $self->executableName instanceof FileName) {
            $self->executableName = FileName::fromString('mf2005');
        }

        if (! $self->listUnit instanceof ListUnit) {
            $self->listUnit = ListUnit::fromInt(2);
        }

        if (! $self->modelWorkSpace instanceof ModelWorkSpace) {
            $self->modelWorkSpace = ModelWorkSpace::fromString('.');
        }

        if (! $self->externalPath instanceof ExternalPath) {
            $self->externalPath = ExternalPath::none();
        }

        if (! $self->verbose instanceof Verbose) {
            $self->verbose = Verbose::fromBool(false);
        }

        return $self;
    }

    public static function fromArray(array $arr): MfPackage
    {
        $self = new self();
        $self->modelname = ModflowModelName::fromString($arr['modelname']);
        $self->nameFileExtension = FileExtension::fromString($arr['namefile_ext']);
        $self->version = ModflowVersion::fromString($arr['version']);
        $self->executableName = FileName::fromString($arr['exe_name']);
        $self->structured = $arr['structured'];
        $self->listUnit = ListUnit::fromInt($arr['listunit']);
        $self->modelWorkSpace = ModelWorkSpace::fromString($arr['model_ws']);
        $self->externalPath = ExternalPath::fromString($arr['external_path']);
        $self->verbose = Verbose::fromBool($arr['verbose']);
        return $self;
    }

    public function toArray(): array
    {
        return array(
            "modelname" => $this->modelname->slugified(),
            "namefile_ext" => $this->nameFileExtension->toString(),
            "version" => $this->version->toString(),
            "exe_name" => $this->executableName->toString(),
            "structured" => $this->structured,
            "listunit" => $this->listUnit->toInt(),
            "model_ws" => $this->modelWorkSpace->toString(),
            "external_path" => $this->externalPath->toString(),
            "verbose" => $this->verbose->toBool()
        );
    }

    /**
     * @return array
     */
    function jsonSerialize()
    {
        return $this->toArray();
    }
}
