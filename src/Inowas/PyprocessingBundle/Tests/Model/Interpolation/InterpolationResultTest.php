<?php

namespace Inowas\PyprocessingBundle\Tests\Model\Interpolation;

use AppBundle\Model\BoundingBox;
use AppBundle\Model\GridSize;
use Inowas\PyprocessingBundle\Model\Interpolation\InterpolationResult;
use Inowas\PyprocessingBundle\Service\Interpolation;

class InterpolationResultTest extends \PHPUnit_Framework_TestCase
{

    public function testInstantiate(){
        $gridSize = new GridSize(1,2);
        $boundingBox = new BoundingBox(1,2,3,4,4326);
        $data = array(array(1,3,4));
        $algorithm = Interpolation::TYPE_GAUSSIAN;

        $interpolationResult = new InterpolationResult(
            $algorithm,
            $data,
            $gridSize,
            $boundingBox
        );

        $this->assertInstanceOf(InterpolationResult::class, $interpolationResult);

        $this->assertEquals($gridSize, $interpolationResult->getGridSize());
        $this->assertEquals($boundingBox, $interpolationResult->getBoundingBox());
        $this->assertEquals($data, $interpolationResult->getData());
        $this->assertEquals($algorithm, $interpolationResult->getAlgorithm());
    }
}
