<?php

namespace AppBundle\Entity;

use AppBundle\Model\ActiveCells;
use AppBundle\Model\StressPeriod;
use CrEOF\Spatial\PHP\Types\Geometry\LineString;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Inowas\PyprocessingBundle\Exception\InvalidArgumentException;
use JMS\Serializer\Annotation as JMS;


class GeneralHeadBoundary
{
    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\Groups({"list", "details", "modelobjectdetails", "modelobjectlist"})
     */
    protected $type = 'GHB';

    /**
     * @var LineString
     *
     * @ORM\Column(name="geometry", type="linestring", nullable=true)
     */
    protected $geometry;

    /**
     * @var ArrayCollection
     *
     * @ORM\Column(name="stress_periods", type="ghb_stress_periods", nullable=true)
     */
    protected $stressPeriods;
    
    /**
     * @var ArrayCollection GeologicalLayer
     *
     * @ORM\ManyToMany(targetEntity="AppBundle\Entity\GeologicalLayer")
     * @ORM\JoinTable(name="general_head_boundaries_geological_layers",
     *     joinColumns={@ORM\JoinColumn(name="general_head_boundary_id", referencedColumnName="id")},
     *     inverseJoinColumns={@ORM\JoinColumn(name="geological_layer_id", referencedColumnName="id")}
     *     )
     * @JMS\Groups({"list", "details", "modelobjectdetails"})
     * @JMS\MaxDepth(2)
     **/
    protected $geologicalLayers;

    /**
     * @param GhbStressPeriod $sp
     * @return $this
     */
    public function addStressPeriod(GhbStressPeriod $sp)
    {
        $this->stressPeriods->add($sp);
        return $this;
    }

    /**
     * @param StressPeriod $stressPeriod
     * @param ActiveCells $activeCells
     * @return array
     */
    public function generateStressPeriodData(StressPeriod $stressPeriod, ActiveCells $activeCells){

        if (! $stressPeriod instanceof GhbStressPeriod){
            throw new InvalidArgumentException(
                'First Argument is supposed to be from Type GhbStressPeriod, %s given.', gettype($stressPeriod)
            );
        }

        $stressPeriodData = array();

        foreach ($activeCells->toArray() as $nRow => $row){
            foreach ($row as $nCol => $value){
                if ($value === true){
                    $stressPeriodData[] = GhbStressPeriodData::create(0, $nRow, $nCol, $stressPeriod->getStage(), $stressPeriod->getCond());
                }
            }
        }

        return $stressPeriodData;
    }
}
