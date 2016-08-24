<?php

namespace AppBundle\Type;

use AppBundle\Model\StressPeriodFactory;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\JsonArrayType;
use Inowas\PyprocessingBundle\Model\Modflow\ValueObject\RivStressPeriod;

/**
 * Class RivStressPeriodsType
 * @package AppBundle\Type
 */
class WelStressPeriodsType extends JsonArrayType
{
    const NAME = 'wel_stress_periods';

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function getName()
    {
        return self::NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function convertToDatabaseValue($value, AbstractPlatform $platform)
    {

        if (null === $value) {
            return null;
        }

        return json_encode($value->toArray());
    }


    /**
     * {@inheritdoc}
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        if ($value === null || $value === '') {
            return null;
        }

        $objs = json_decode($value, true);

        $sps = new ArrayCollection();
        foreach ($objs as $obj) {
            $sps->add(StressPeriodFactory::createWel()
                ->setDateTimeBegin(new \DateTime($obj['dateTimeBegin']))
                ->setDateTimeEnd(new \DateTime($obj['dateTimeEnd']))
                ->setNumberOfTimeSteps($obj['numberOfTimeSteps'])
                ->setSteady($obj['steady'])
                ->setTimeStepMultiplier($obj['timeStepMultiplier'])
                ->setFlux($obj['flux'])
            );
        }

        return $sps;
    }

    /**
     * {@inheritdoc}
     * @codeCoverageIgnore
     */
    public function requiresSQLCommentHint(AbstractPlatform $platform)
    {
        return true;
    }
}
