<?php

namespace Inowas\ScenarioAnalysisBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use FOS\UserBundle\Model\UserInterface;
use Inowas\ModflowBundle\Model\ModflowModel;
use Ramsey\Uuid\Uuid;

class ScenarioAnalysis
{
    /**
     * @var Uuid
     */
    protected $id;

    /**
     * @var Uuid
     */
    protected $userId;

    /**
     * @var Uuid
     */
    protected $baseModelId;

    /**
     * @var ArrayCollection
     */
    protected $scenarios;

    /**
     * ScenarioAnalysis constructor.
     * @param UserInterface $user
     * @param ModflowModel $model
     */
    public function __construct(UserInterface $user, ModflowModel $model)
    {
        $this->baseModelId = $model->getId();
        $this->userId = $user->getId();
        $this->id = Uuid::uuid4();
    }

    /**
     * @return Uuid
     */
    public function getId(): Uuid
    {
        return $this->id;
    }

    /**
     * @param Uuid $id
     * @return ScenarioAnalysis
     */
    public function setId(Uuid $id): ScenarioAnalysis
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return Uuid
     */
    public function getUserId(): Uuid
    {
        return $this->userId;
    }

    /**
     * @param Uuid $userId
     * @return ScenarioAnalysis
     */
    public function setUserId(Uuid $userId): ScenarioAnalysis
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return Uuid
     */
    public function getBaseModelId(): Uuid
    {
        return $this->baseModelId;
    }

    /**
     * @param Uuid $baseModelId
     * @return ScenarioAnalysis
     */
    public function setBaseModelId(Uuid $baseModelId): ScenarioAnalysis
    {
        $this->baseModelId = $baseModelId;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getScenarios(): Collection
    {
        return $this->scenarios;
    }

    /**
     * @param ArrayCollection $scenarios
     * @return ScenarioAnalysis
     */
    public function setScenarios(ArrayCollection $scenarios): ScenarioAnalysis
    {
        $this->scenarios = $scenarios;
        return $this;
    }

    /**
     * @param Scenario $scenario
     * @return $this
     */
    public function addScenario(Scenario $scenario){
        $this->scenarios[] = $scenario;
        return $this;
    }

    /**
     * @param Scenario $scenario
     * @return $this
     */
    public function removeScenario(Scenario $scenario){
        if ($this->scenarios->contains($scenario)){
            $this->scenarios->removeElement($scenario);
        }

        $this->scenarios = new ArrayCollection($this->scenarios->toArray());
        return $this;
    }
}
