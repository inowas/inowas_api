<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Model\Packages;

use Inowas\Common\FileSystem\FileName;
use Inowas\Common\Modflow\Extension;
use Inowas\Common\Modflow\FileFormat;
use Inowas\Common\Modflow\FileHeader;
use Inowas\Common\Modflow\Filenames;
use Inowas\Common\Modflow\PackageFlows;
use Inowas\Common\Modflow\Unitnumber;

/**
 * Class LmfPackage
 * @package Inowas\ModflowModel\Model\Packages
 */
class LmfPackage extends AbstractPackage
{
    /** @var string */
    public const TYPE = 'lmf';

    /** @var string */
    public const DESCRIPTION = 'MODFLOW Link-MT3DMS Package';

    /** @var  FileName */
    private $outputFileName;

    /** @var  Unitnumber */
    private $outputFileUnit;

    /** @var  FileHeader */
    private $outputFileHeader;

    /** @var  FileFormat */
    private $outputFileFormat;

    /** @var  Extension */
    private $extension;

    /** @var PackageFlows */
    private $packageFlows;

    /** @var Unitnumber */
    private $unitnumber;

    /** @var Filenames */
    private $filenames;

    /**
     * @return LmfPackage
     */
    public static function fromDefaults(): LmfPackage
    {
        return new self(
            FileName::fromString('mt3d_link.ftl'),
            Unitnumber::fromInteger(54),
            FileHeader::fromString('extended'),
            FileFormat::fromString('unformatted'),
            Extension::fromString('lmt6'),
            PackageFlows::fromArray([]),
            Unitnumber::fromValue(null),
            Filenames::fromValues(null)
        );
    }

    /**
     * @noinspection MoreThanThreeArgumentsInspection
     *
     * @param Filename $outputFileName
     * @param Unitnumber $outputFileUnit
     * @param FileHeader $outputFileHeader
     * @param FileFormat $outputFileFormat
     * @param Extension $extension
     * @param PackageFlows $packageFlows
     * @param Unitnumber $unitnumber
     * @param Filenames $filenames
     *
     * @return LmfPackage
     */
    public static function fromParams(
        Filename $outputFileName,
        Unitnumber $outputFileUnit,
        FileHeader $outputFileHeader,
        FileFormat $outputFileFormat,
        Extension $extension,
        PackageFlows $packageFlows,
        Unitnumber $unitnumber,
        Filenames $filenames
    ): LmfPackage
    {
        return new self(
            $outputFileName,
            $outputFileUnit,
            $outputFileHeader,
            $outputFileFormat,
            $extension,
            $packageFlows,
            $unitnumber,
            $filenames
        );
    }

    /**
     * @param array $arr
     * @return LmfPackage
     */
    public static function fromArray(array $arr): LmfPackage
    {
        return new self(
            FileName::fromString($arr['output_file_name']),
            Unitnumber::fromInteger($arr['output_file_unit']),
            FileHeader::fromString($arr['output_file_header']),
            FileFormat::fromString($arr['output_file_format']),
            Extension::fromString($arr['extension']),
            PackageFlows::fromArray($arr['package_flows']),
            Unitnumber::fromValue($arr['unitnumber']),
            Filenames::fromValues($arr['filenames'])
        );
    }

    /**
     * LmfPackage constructor.
     * @param FileName $outputFileName
     * @param Unitnumber $outputFileUnit
     * @param FileHeader $outputFileHeader
     * @param FileFormat $outputFileFormat
     * @param Extension $extension
     * @param PackageFlows $packageFlows
     * @param Unitnumber $unitNumber
     * @param Filenames $filenames
     */
    private function __construct(
        FileName $outputFileName,
        Unitnumber $outputFileUnit,
        FileHeader $outputFileHeader,
        FileFormat $outputFileFormat,
        Extension $extension,
        PackageFlows $packageFlows,
        Unitnumber $unitNumber,
        Filenames $filenames
    )
    {
        $this->outputFileName = $outputFileName;
        $this->outputFileUnit = $outputFileUnit;
        $this->outputFileHeader = $outputFileHeader;
        $this->outputFileFormat = $outputFileFormat;
        $this->extension = $extension;
        $this->packageFlows = $packageFlows;
        $this->unitnumber = $unitNumber;
        $this->filenames = $filenames;
    }

    /**
     * @return FileName
     */
    public function outputFileName(): FileName
    {
        return $this->outputFileName;
    }

    /**
     * @return Unitnumber
     */
    public function outputFileUnit(): Unitnumber
    {
        return $this->outputFileUnit;
    }

    /**
     * @return FileHeader
     */
    public function outputFileHeader(): FileHeader
    {
        return $this->outputFileHeader;
    }

    /**
     * @return FileFormat
     */
    public function outputFileFormat(): FileFormat
    {
        return $this->outputFileFormat;
    }

    /**
     * @return Extension
     */
    public function extension(): Extension
    {
        return $this->extension;
    }

    /**
     * @return PackageFlows
     */
    public function packageFlows(): PackageFlows
    {
        return $this->packageFlows;
    }

    /**
     * @return Unitnumber
     */
    public function unitnumber(): Unitnumber
    {
        return $this->unitnumber;
    }

    /**
     * @return Filenames
     */
    public function filenames(): Filenames
    {
        return $this->filenames;
    }

    /**
     * @param FileName $outputFileName
     * @return LmfPackage
     */
    public function updateOutputFileName(FileName $outputFileName): LmfPackage
    {
        $package = self::fromArray($this->toArray());
        $package->outputFileName = $outputFileName;
        return $package;
    }

    /**
     * @param Unitnumber $outputFileUnit
     * @return LmfPackage
     */
    public function updateOutputFileUnit(Unitnumber $outputFileUnit): LmfPackage
    {
        $package = self::fromArray($this->toArray());
        $package->outputFileUnit = $outputFileUnit;
        return $package;
    }

    /**
     * @param FileHeader $outputFileHeader
     * @return LmfPackage
     */
    public function updateOutputFileHeader(FileHeader $outputFileHeader): LmfPackage
    {
        $package = self::fromArray($this->toArray());
        $package->outputFileHeader = $outputFileHeader;
        return $package;
    }

    /**
     * @param FileFormat $outputFileFormat
     * @return LmfPackage
     */
    public function updateOutputFileFormat(FileFormat $outputFileFormat): LmfPackage
    {
        $package = self::fromArray($this->toArray());
        $package->outputFileFormat = $outputFileFormat;
        return $package;
    }

    /**
     * @param Extension $extension
     * @return LmfPackage
     */
    public function updateExtension(Extension $extension): LmfPackage
    {
        $package = self::fromArray($this->toArray());
        $package->extension = $extension;
        return $package;
    }

    /**
     * @param PackageFlows $packageFlows
     * @return LmfPackage
     */
    public function updatePackageFlows(PackageFlows $packageFlows): LmfPackage
    {
        $package = self::fromArray($this->toArray());
        $package->packageFlows = $packageFlows;
        return $package;
    }

    /**
     * @param Unitnumber $unitnumber
     * @return LmfPackage
     */
    public function updateUnitnumber(Unitnumber $unitnumber): LmfPackage
    {
        $package = self::fromArray($this->toArray());
        $package->unitnumber = $unitnumber;
        return $package;
    }

    /**
     * @param Filenames $filenames
     * @return LmfPackage
     */
    public function updateFilenames(Filenames $filenames): LmfPackage
    {
        $package = self::fromArray($this->toArray());
        $package->filenames = $filenames;
        return $package;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return true;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'output_file_name' => $this->outputFileName->toString(),
            'output_file_unit' => $this->outputFileUnit->toValue(),
            'output_file_header' => $this->outputFileHeader->toValue(),
            'output_file_format' => $this->outputFileFormat->toValue(),
            'extension' => $this->extension->toValue(),
            'package_flows' => $this->packageFlows->toArray(),
            'unitnumber' => $this->unitnumber->toValue(),
            'filenames' => $this->filenames->toValues(),
        ];
    }

    /**
     * @return array
     */
    public function getEditables(): array
    {
        return [];
    }

    /**
     * @param array $arr
     */
    public function mergeEditables(array $arr): void
    {}

    /**
     * @return array
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}
