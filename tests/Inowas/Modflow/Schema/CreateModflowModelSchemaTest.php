<?php

declare(strict_types=1);

namespace Tests\Inowas\Modflow\Schema;

use Inowas\Common\SchemaValidator\UrlReplaceLoader;
use League\JsonGuard\Validator;
use League\JsonReference\Dereferencer;
use PHPUnit_Framework_TestCase as BaseTestCase;

class CreateModflowModelSchemaTest extends BaseTestCase
{
    public function providerModel()
    {
        $path = __DIR__.'/_files/';

        return [
            [
                file_get_contents($path . 'createModflowModel.json'),
                file_get_contents('spec/schema/modflow/command/createModflowModel.json'),
                true
            ]
        ];
    }

    /**
     * @dataProvider providerModel
     * @test
     * @param string $json
     * @param string $schema
     * @param bool $expected
     */
    public function it_validates_create_model_command(string $json, string $schema, bool $expected)
    {
        $dereferencer = Dereferencer::draft4();
        $dereferencer->getLoaderManager()->registerLoader('https', new UrlReplaceLoader());
        $dereferencedSchema = $dereferencer->dereference(json_decode($schema));

        $validator = new Validator(json_decode($json), $dereferencedSchema);
        $this->assertSame($expected, $validator->passes(), var_export($validator->errors(), true));
    }
}
