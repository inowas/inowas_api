<?php

namespace AppBundle\Tests\Entity;

use AppBundle\Entity\Calculation;
use AppBundle\Model\CalculationFactory;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class SimpleRasterTest extends WebTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    protected $entityManager;

    /**
     * @var Calculation $project
     */
    protected $calculation;

    /**
     * {@inheritDoc}
     */
    public function setUp()
    {
        self::bootKernel();
        $this->entityManager = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager()
        ;
    }

    /**
     *
     */
    public function testAddMultipleArrayToCalculatedValues()
    {
        $submittedValues = array(
            array(1,2,3),
            array(4,5,6),
            array(7,8,9)
        );

        $this->calculation = CalculationFactory::create();
        $this->calculation->setValues($submittedValues);
        $this->entityManager->persist($this->calculation);
        $this->entityManager->flush();
        $this->entityManager->clear();

        $calculation = $this->entityManager->getRepository('AppBundle:Calculation')
            ->findOneBy(array(
                'id' => $this->calculation->getId()
            ))
        ;

        $values = $calculation->getValues();
        $this->assertEquals($submittedValues, $values);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
    }
}
