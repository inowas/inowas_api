<?php

namespace Inowas\PyprocessingBundle\Model;

use Ramsey\Uuid\Uuid;
use Symfony\Component\Process\Process;

class ProcessWrapper
{
    /** @var  Uuid */
    protected $id;

    /** @var Process */
    protected $process;

    /**
     * ProcessWrapper constructor.
     * @param Process $process
     * @param Uuid $id
     */
    public function __construct(Process $process, Uuid $id)
    {
        $this->process = $process;
        $this->id = $id;
    }

    /**
     * @return Uuid
     */
    public function getId(): Uuid
    {
        return $this->id;
    }

    /**
     * @return Process
     */
    public function getProcess(): Process
    {
        return $this->process;
    }
}