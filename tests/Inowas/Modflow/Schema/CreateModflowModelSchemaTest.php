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
            [file_get_contents($path . 'createModflowModel.json'), true]
        ];
    }

    /**
     * @dataProvider providerModel
     * @test
     * @param string $json
     * @param bool $expected
     */
    public function it_validates_create_model_command(string $json, bool $expected)
    {
        $jsonSchema = str_replace(
            'https://inowas.com/',
            'file://spec/',
            file_get_contents('spec/schema/modflow/command/createModflowModel.json')
        );

        $dereferencer = Dereferencer::draft4();
        $dereferencer->getLoaderManager()->registerLoader('https', new UrlReplaceLoader());
        $schema = $dereferencer->dereference(json_decode($jsonSchema));

        $validator = new Validator(json_decode($json), $schema);
        $this->assertSame($expected, $validator->passes(), var_export($validator->errors(), true));
    }
}
