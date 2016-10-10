<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Ramsey\Uuid\Uuid;

/**
 * AbstractModel
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity()
 * @ORM\Table(name="models")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
 *      "modflow" = "ModFlowModel",
 *      "soil" = "SoilModel"
 * })
 * @JMS\ExclusionPolicy("none")
 */
abstract class AbstractModel
{
    /**
     * @var Uuid
     *
     * @ORM\Id
     * @ORM\Column(name="id", type="uuid", unique=true)
     * @JMS\Type("string")
     * @JMS\Groups({"list", "details", "modeldetails", "modelobjectdetails", "soilmodellist", "soilmodeldetails"})
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string",length=255)
     * @JMS\Groups({"list", "details", "modeldetails", "soilmodellist", "soilmodeldetails"})
     */
    protected $name = "";

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * @JMS\Groups({"list", "details", "modeldetails", "soilmodellist", "soilmodeldetails"})
     */
    protected $description = "";

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User")
     * @ORM\JoinColumn(name="owner_id", referencedColumnName="id", onDelete="CASCADE")
     * @JMS\MaxDepth(1)
     * @JMS\Groups({"details", "modeldetails", "soilmodellist", "soilmodeldetails"})
     */
    protected $owner;

    /**
     * @var boolean
     *
     * @ORM\Column(name="public", type="boolean")
     * @JMS\Groups({"list", "modeldetails", "soilmodellist", "soilmodeldetails"})
     */
    protected $public;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateCreated", type="datetime")
     * @JMS\Groups({"list", "details", "soilmodellist",  "soilmodeldetails"})
     */
    protected $dateCreated;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateModified", type="datetime")
     * @JMS\Groups({"list", "details", "soilmodellist",  "soilmodeldetails"})
     */
    protected $dateModified;

    /**
     * @var ArrayCollection ModelObject $modelObjects
     *
     * @ORM\ManyToMany(targetEntity="ModelObject", cascade={"persist", "remove"})
     * @ORM\JoinTable(name="models_model_objects",
     *      joinColumns={@ORM\JoinColumn(name="model_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="model_object_id", referencedColumnName="id", onDelete="CASCADE")}
     *      )
     **/
    protected $modelObjects;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->public = true;
        $this->dateCreated = new \DateTime();
        $this->dateModified = new \DateTime();
        $this->modelObjects = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return Uuid
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     *
     * @return $this
     */
    public function setName($name = null)
    {
        if (is_null($name)){
            $name = "";
        }

        $this->name = $name;
        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getSanitizedName(){
        $str = $this->name;
        $str = strip_tags($str);
        $str = preg_replace('/[\r\n\t ]+/', ' ', $str);
        $str = preg_replace('/[\"\*\/\:\<\>\?\'\|]+/', ' ', $str);
        $str = strtolower($str);
        $str = html_entity_decode( $str, ENT_QUOTES, "utf-8" );
        $str = htmlentities($str, ENT_QUOTES, "utf-8");
        $str = preg_replace("/(&)([a-z])([a-z]+;)/i", '$2', $str);
        $str = str_replace(' ', '-', $str);
        $str = rawurlencode($str);
        $str = str_replace('%', '-', $str);
        return $str;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return $this
     */
    public function setDescription($description = null)
    {
        if (is_null($description)){
            $description = "";
        }
        $this->description = $description;
        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set public
     *
     * @param boolean $public
     *
     * @return $this
     */
    public function setPublic($public)
    {
        $this->public = $public;

        return $this;
    }

    /**
     * Get public
     *
     * @return boolean
     */
    public function getPublic()
    {
        return $this->public;
    }

    /**
     * Get dateCreated
     *
     * @return \DateTime
     */
    public function getDateCreated()
    {
        return $this->dateCreated;
    }

    /**
     * Set dateModified
     *
     * @param \DateTime $dateModified
     *
     * @return $this
     */
    public function setDateModified($dateModified)
    {
        $this->dateModified = $dateModified;

        return $this;
    }

    /**
     * Get dateModified
     *
     * @return \DateTime
     */
    public function getDateModified()
    {
        return $this->dateModified;
    }

    /**
     * Set owner
     *
     * @param User $owner
     *
     * @return $this
     */
    public function setOwner(User $owner = null)
    {
        $this->owner = $owner;

        return $this;
    }

    /**
     * Get owner
     *
     * @return User
     */
    public function getOwner()
    {
        return $this->owner;
    }

    /**
     * Add soilModelObject
     *
     * @param \AppBundle\Entity\ModelObject $modelObject
     * @return $this
     */
    public function addModelObject(ModelObject $modelObject)
    {
        if (!$this->modelObjects->contains($modelObject)){
            $this->modelObjects[] = $modelObject;
        }

        return $this;
    }

    /**
     * Remove soilModelObject
     *
     * @param \AppBundle\Entity\ModelObject $modelObject
     * @return $this
     */
    public function removeModelObject(ModelObject $modelObject)
    {
        if ($this->modelObjects->contains($modelObject)){
            $this->modelObjects->removeElement($modelObject);
        }
        return $this;
    }

    /**
     * Get modelObjects
     *
     * @return ArrayCollection
     */
    public function getModelObjects()
    {
        return $this->modelObjects;
    }
}
