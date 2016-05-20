<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Model\Interpolation\BoundingBox;
use AppBundle\Model\Interpolation\GridSize;
use AppBundle\Model\Interpolation\KrigingInterpolation;
use AppBundle\Model\Interpolation\PointValue;
use JMS\Serializer\Serializer;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Bundle\TwigBundle\TwigEngine;

class InterpolationTest extends WebTestCase
{

    /** @var  Serializer $serializer */
    protected $serializer;

    /** @var  TwigEngine */
    protected $templating;

    /** @var string */
    protected $url = "http://app.dev.inowas.com/cgi-bin/pywps.cgi";

    public function setUp()
    {
        self::bootKernel();

        $this->serializer = static::$kernel->getContainer()
            ->get('serializer')
        ;

        $this->templating = static::$kernel->getContainer()
            ->get('templating');
    }

    public function testDataWillBeRendered()
    {
        $ki = new KrigingInterpolation(new GridSize(12, 13), new BoundingBox(1.2, 1.2, 2.1, .2));
        $ki->addPoint(new PointValue(1.1, 2.2, 3.4));
        $ki->addPoint(new PointValue(4.4, 5.5, 6.6));
        $serializedKi = $this->serializer->serialize($ki, 'json');
        $serializedKi = str_replace('"', '\'', $serializedKi);

        $content = $this->templating->render(':inowas/WPS:interpolation.xml.twig', array(
            'jsonData' => $serializedKi
        ));

        $this->assertContains("{'type':'kriging','bounding_box':{'x_min':1.2,'x_max':1.2,'y_min':2.1,'y_max':0.2},'grid_size':{'n_x':12,'n_y':13},'point_values':[{'x':1.1,'y':2.2,'value':3.4},{'x':4.4,'y':5.5,'value':6.6}]}", $content);
    }

    public function testIfServiceIsAvailable()
    {
        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $this->url.'?service=wps&request=getcapabilities');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        //execute post
        $response = curl_exec($ch);
        //close connection
        curl_close($ch);

        $this->assertContains('<ows:Identifier>interpolation</ows:Identifier>', $response);
    }

    public function testDataWillBeSent()
    {
        $numberOfColumns = 15;
        $numberOfRows = 15;

        $ki = new KrigingInterpolation(new GridSize($numberOfColumns, $numberOfRows), new BoundingBox(1.2, 1.2, 2.1, .2));
        $ki->addPoint(new PointValue(1.1, 2.2, 3.4));
        $ki->addPoint(new PointValue(4.4, 5.5, 6.6));
        $serializedKi = $this->serializer->serialize($ki, 'json');
        $serializedKi = str_replace('"', '\'', $serializedKi);

        $content = $this->templating->render(':inowas/WPS:interpolation.xml.twig', array(
            'jsonData' => $serializedKi
        ));

        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, 'http://app.dev.inowas.com/cgi-bin/pywps.cgi');
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $content);

        //execute post
        $response = curl_exec($ch);
        //close connection
        curl_close($ch);

        $raster = json_decode($response)->raster;
        $this->assertCount($numberOfRows, $raster);
        $this->assertCount($numberOfColumns, $raster[0]);
    }
}
