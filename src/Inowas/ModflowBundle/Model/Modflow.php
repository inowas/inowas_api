<?php

namespace Inowas\ModflowBundle\Model;
use Ramsey\Uuid\Uuid;

class Modflow
{

    /** @var  Uuid */
    protected $id;

    /** @var  ModflowModel */
    protected $modflowModel;

    /** @var  Uuid */
    protected $userId;

    /** @var  bool */
    protected $public;

    /**
     * Modflow constructor.
     */
    public function __construct()
    {
        $this->id = Uuid::uuid4();
        $this->public = true;
    }

    /**
     * @return Uuid
     */
    public function getId(): Uuid
    {
        return $this->id;
    }

    /**
     * @return ModflowModel
     */
    public function getModflowModel(): ModflowModel
    {
        return $this->modflowModel;
    }

    /**
     * @param ModflowModel $modflowModel
     * @return Modflow
     */
    public function setModflowModel(ModflowModel $modflowModel): Modflow
    {
        $this->modflowModel = $modflowModel;
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
     * @return Modflow
     */
    public function setUserId(Uuid $userId): Modflow
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return bool
     */
    public function isPublic(): bool
    {
        return $this->public;
    }

    /**
     * @param bool $public
     * @return Modflow
     */
    public function setPublic(bool $public): Modflow
    {
        $this->public = $public;
        return $this;
    }
}
