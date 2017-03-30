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

class MfPackage implements PackageInterface
{

    /** @var string  */
    protected $type = 'mf';

    /** @var  ModflowModelName */
    protected $modelname;

    /** @var  FileExtension */
    protected $nameFileExtension;

    /** @var ModflowVersion */
    protected $version;

    /** @var FileName */
    protected $executableName;

    /** @var ListUnit  */
    protected $listUnit;

    /** @var ModelWorkSpace */
    protected $modelWorkSpace;

    /** @var ExternalPath */
    protected $externalPath;

    /** @var Verbose $verbose  */
    protected $verbose;

    public static function fromDefaults()
    {
        $name = ModflowModelName::fromString('testmodel');
        $fileExtension = FileExtension::fromString('nam');
        $version = ModflowVersion::fromString(ModflowVersion::MF2005);
        $executableName = FileName::fromString('mf2005');
        $listUnit = ListUnit::fromInt(2);
        $modelWorkSpace = ModelWorkSpace::fromString('.');
        $externalPath = ExternalPath::fromValue(null);
        $verbose = Verbose::fromBool(false);

        return new self($name, $fileExtension, $version, $executableName, $listUnit, $modelWorkSpace, $externalPath, $verbose);
    }

    public static function fromParams(
        ModflowModelName $name,
        FileExtension $fileExtension,
        ModflowVersion $version,
        FileName $executableName,
        ListUnit $listUnit,
        ModelWorkSpace $modelWorkSpace,
        ExternalPath $externalPath,
        Verbose $verbose
    ): MfPackage
    {
        return new self($name, $fileExtension, $version, $executableName, $listUnit, $modelWorkSpace, $externalPath, $verbose);
    }

    public static function fromArray(array $arr): MfPackage
    {
        $name = ModflowModelName::fromString($arr['modelname']);
        $fileExtension = FileExtension::fromString($arr['namefile_ext']);
        $version = ModflowVersion::fromString($arr['version']);
        $executableName = FileName::fromString($arr['exe_name']);
        $listUnit = ListUnit::fromInt($arr['listunit']);
        $modelWorkSpace = ModelWorkSpace::fromString($arr['model_ws']);
        $externalPath = ExternalPath::fromValue($arr['external_path']);
        $verbose = Verbose::fromBool($arr['verbose']);

        return new self($name, $fileExtension, $version, $executableName, $listUnit, $modelWorkSpace, $externalPath, $verbose);
    }

    private function __construct(
        ModflowModelName $name,
        FileExtension $fileExtension,
        ModflowVersion $version,
        FileName $executableName,
        ListUnit $listUnit,
        ModelWorkSpace $modelWorkSpace,
        ExternalPath $externalPath,
        Verbose $verbose
    )
    {
        $this->modelname = $name;
        $this->nameFileExtension = $fileExtension;
        $this->version = $version;
        $this->executableName = $executableName;
        $this->listUnit = $listUnit;
        $this->modelWorkSpace = $modelWorkSpace;
        $this->externalPath = $externalPath;
        $this->verbose = $verbose;
    }

    public function type(): string
    {
        return $this->type;
    }

    public function modelname(): ModflowModelName
    {
        return $this->modelname;
    }

    public function nameFileExtension(): FileExtension
    {
        return $this->nameFileExtension;
    }

    public function version(): ModflowVersion
    {
        return $this->version;
    }

    public function executableName(): FileName
    {
        return $this->executableName;
    }

    public function listUnit(): ListUnit
    {
        return $this->listUnit;
    }

    public function modelWorkSpace(): ModelWorkSpace
    {
        return $this->modelWorkSpace;
    }

    public function externalPath(): ExternalPath
    {
        return $this->externalPath;
    }

    public function verbose(): Verbose
    {
        return $this->verbose;
    }

    public function toArray(): array
    {
        return array(
            "modelname" => $this->modelname->slugified(),
            "namefile_ext" => $this->nameFileExtension->toString(),
            "version" => $this->version->toString(),
            "exe_name" => $this->executableName->toString(),
            "listunit" => $this->listUnit->toInt(),
            "model_ws" => $this->modelWorkSpace->toString(),
            "external_path" => $this->externalPath->toValue(),
            "verbose" => $this->verbose->toBool()
        );
    }

    public function updateModelName(ModflowModelName $name): MfPackage
    {
        $this->modelname = $name;
        return self::fromArray($this->toArray());
    }

    public function updateVersion(ModflowVersion $version): MfPackage
    {
        $this->version = $version;
        return self::fromArray($this->toArray());
    }

    public function updateExecutableName(FileName $name): MfPackage
    {
        $this->executableName = $name;
        return self::fromArray($this->toArray());
    }

    public function updateListUnit(ListUnit $listUnit): MfPackage
    {
        $this->listUnit = $listUnit;
        return self::fromArray($this->toArray());
    }

    public function updateModelWorkSpace(ModelWorkSpace $workSpace): MfPackage
    {
        $this->modelWorkSpace = $workSpace;
        return self::fromArray($this->toArray());
    }

    /**
     * @return array
     */
    function jsonSerialize()
    {
        return $this->toArray();
    }
}
