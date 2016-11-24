<?php

namespace Inowas\ModflowBundle\Service;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManager;
use Inowas\ModflowBundle\Exception\InvalidArgumentException;
use Inowas\ModflowBundle\Model\Boundary\ObservationPoint;
use Inowas\ModflowBundle\Model\Boundary\ObservationPointFactory;
use Inowas\ModflowBundle\Model\StressPeriodFactory;
use Ramsey\Uuid\Uuid;

class ObservationPointManager
{
    /**
     * @var EntityManager $entityManager
     */
    private $entityManager;

    public function __construct(EntityManager $entityManager){
        $this->entityManager = $entityManager;
    }

    /**
     * @return ObservationPoint
     */
    public function create(){
        return ObservationPointFactory::create();
    }

    /**
     * @param $id
     * @return ObservationPoint|null
     */
    public function findById($id){
        if (! Uuid::isValid($id)){
            throw new InvalidArgumentException('The given id is not a valid Uuid.');
        }

        return $this->entityManager
            ->getRepository('InowasModflowBundle:Boundary\ObservationPoint')
            ->findOneBy(array(
                'id' => $id
            ));
    }

    /**
     * @param ObservationPoint $observationPoint
     * @param string $stressPeriodsJson
     * @return ObservationPoint
     */
    public function updateStressPeriods(ObservationPoint $observationPoint, string $stressPeriodsJson){
        $stressPeriodsData = json_decode($stressPeriodsJson);

        if (! objectHasAttribute('type', $stressPeriodsData)){
            throw new InvalidArgumentException('Wrong Json-Format, Attribute type expected.');
        }

        $type = $stressPeriodsData->type;

        if (! objectHasAttribute('data', $stressPeriodsData)){
            throw new InvalidArgumentException('Wrong Json-Format, Attribute data expected.');
        }

        if (! is_array($stressPeriodsData->data) ){
            throw new InvalidArgumentException('Wrong Json-Format, Attribute data has to be an array.');
        }

        $stressPeriods = new ArrayCollection();
        foreach ($stressPeriodsData->data as $stressPeriodData){
            $stressPeriod = StressPeriodFactory::createFromArray($type, $stressPeriodData);
            $stressPeriods->add($stressPeriod);
        }

        $observationPoint->setStressPeriods($stressPeriods);
        $this->entityManager->persist($observationPoint);
        $this->entityManager->flush();
        return $observationPoint;
    }

    /**
     * @param ObservationPoint $observationPoint
     * @return ObservationPoint
     */
    public function update(ObservationPoint $observationPoint){
        $this->entityManager->persist($observationPoint);
        $this->entityManager->flush();
        return $observationPoint;
    }

    /**
     * @param ObservationPoint $observationPoint
     * @return ObservationPoint
     */
    public function remove(ObservationPoint $observationPoint){
        $this->entityManager->remove($observationPoint);
        $this->entityManager->flush();
        return $observationPoint;
    }
}
