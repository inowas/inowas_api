<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

/**
 * AbstractKernel
 *
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity()
 * @ORM\Table(name="kernels")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({  "modflow" = "ModFlowKernel" })
 * @JMS\ExclusionPolicy("all")
 */
abstract class AbstractKernel
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
}
