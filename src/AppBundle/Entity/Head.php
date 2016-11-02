<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Ramsey\Uuid\Uuid;

/**
 * @ORM\Entity(repositoryClass="AppBundle\Repository\HeadRepository")
 * @ORM\Table(name="heads")
 */
class Head
{
    /**
     * @var uuid
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="uuid", unique=true)
     * @JMS\Type("string")
     * @JMS\Groups({"list", "details"})
     */
    private $id;

    /**
     * @var uuid
     *
     * @ORM\Id
     * @ORM\Column(name="model_id", type="uuid")
     */
    private $modelId;

    /**
     * @var float
     *
     * @ORM\Column(name="min", type="float")
     * @JMS\Groups({"list", "details"})
     */
    private $min;

    /**
     * @var float
     *
     * @ORM\Column(name="max", type="float")
     * @JMS\Groups({"list", "details"})
     */
    private $max;
    /**
     * @var integer
     *
     * @ORM\Column(name="layer", type="integer")
     * @JMS\Groups({"list", "details"})
     **/
    private $layer;

    /**
     * @var integer
     *
     * @ORM\Column(name="totim", type="integer")
     * @JMS\Groups({"list", "details"})
     */
    private $totim;

    /**
     * @var array
     *
     * @ORM\Column(name="data", type="json_array")
     * @JMS\Groups({"details"})
     */
    private $data;

    public function __construct()
    {
        $this->id = Uuid::uuid4();
    }

    /**
     * @param Uuid $modelId
     * @return Head
     */
    public function setModelId(Uuid $modelId): Head
    {
        $this->modelId = $modelId;
        return $this;
    }

    /**
     * @return Uuid
     */
    public function getModelId(): Uuid
    {
        return $this->modelId;
    }

    /**
     * @return int
     */
    public function getLayer(): int
    {
        return $this->layer;
    }

    /**
     * @param int $layer
     * @return Head
     */
    public function setLayer(int $layer): Head
    {
        $this->layer = $layer;
        return $this;
    }

    /**
     * @return Uuid
     */
    public function getId(): Uuid
    {
        return $this->id;
    }

    public function setTotim(int $totim){
        $this->totim = $totim;
        return $this;
    }

    /**
     * @return int
     */
    public function getTotim(): int
    {
        return $this->totim;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setData(array $data)
    {
        $this->data = $data;

        foreach ($data as $nRow => $row){
            foreach ($row as $nCol => $value) {
                if (is_null($this->min)){
                    $this->min = $value;
                }

                if (! is_null($this->min) && ! is_null($value)){
                    if ($value < $this->min){
                        $this->min = $value;
                    }
                }

                if (is_null($this->max)){
                    $this->max = $value;
                }

                if (! is_null($this->max) && ! is_null($value)){
                    if ($value > $this->max ){
                        $this->max = $value;
                    }
                }
            }
        }

        return $this;
    }

    /**
     * @return float
     */
    public function getMin(): float
    {
        return $this->min;
    }

    /**
     * @return float
     */
    public function getMax(): float
    {
        return $this->max;
    }
}
