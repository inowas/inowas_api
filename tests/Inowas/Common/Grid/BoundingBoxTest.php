<?php

namespace Tests\Inowas\Common\Grid;

use Inowas\Common\Grid\BoundingBox;

class BoundingBoxTest extends \PHPUnit_Framework_TestCase
{
    public function test_same_as(){
        $box1 = BoundingBox::fromCoordinates(1,2,3,4,5,6,7);
        $box2 = BoundingBox::fromCoordinates(1,2,3,4,5,6,7);
        $this->assertTrue($box1->sameAs($box2));
        $this->assertTrue($box2->sameAs($box1));

        $box3 = BoundingBox::fromCoordinates(2,2,3,4,5,6,7);
        $this->assertFalse($box2->sameAs($box3));
        $this->assertFalse($box3->sameAs($box2));
    }
}
