<?php

declare(strict_types=1);

namespace Tests\Inowas\Common\Boundaries;

use League\JsonGuard\Validator;
use League\JsonReference\Dereferencer;
use PHPUnit_Framework_TestCase as BaseTestCase;

class WellBoundarySchemaTest extends BaseTestCase
{
    public function providerWellType()
    {
        return [
            ['puw', true],
            ['invalid', false],
        ];
    }

    /**
     * @dataProvider providerWellType
     * @test
     * @param string $type
     * @param bool $expected
     */
    public function it_validates_well_type(string $type, bool $expected)
    {
        $dereferencer = Dereferencer::draft4();
        $schema = $dereferencer->dereference('file://spec/schema/modflow/boundary/wellType.json');

        $data = json_encode(['well_type' => $type]);

        $validator = new Validator(json_decode($data), $schema);

        $this->assertSame($expected, $validator->passes(), var_export($validator->errors(), true));
    }

    public function providerWell()
    {
        $path = 'tests/Inowas/Common/Boundaries/_files/';

        return [
            [file_get_contents($path . 'well.json'), true],
//            [file_get_contents($path . 'well_invalid.json'), false],
        ];
    }

    /**
     * @dataProvider providerWell
     * @test
     * @param string $json
     * @param bool $expected
     */
    public function it_validates_well(string $json, bool $expected)
    {
        $jsonSchema = str_replace(
            'https://inowas.com/',
            'file://spec/',
            file_get_contents('spec/schema/modflow/boundary/well.json')
        );
        $dereferencer = Dereferencer::draft4();
        $schema = $dereferencer->dereference(json_decode($jsonSchema));

        $validator = new Validator(json_decode($json), $schema);

        $this->assertSame($expected, $validator->passes(), var_export($validator->errors(), true));
    }

}
