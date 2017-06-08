<?php

declare(strict_types=1);

namespace Inowas\Project\Model\Handler;

use Inowas\ModflowBundle\Exception\NotFoundException;
use Inowas\Project\Infrastructure\Projection\ProjectsFinder;
use Inowas\Project\Model\Command\CloneProject;

final class CloneProjectHandler
{

    /** @var  ProjectsFinder */
    private $projectsFinder;

    public function __construct(ProjectsFinder $finder)
    {
        $this->projectsFinder = $finder;
    }

    public function __invoke(CloneProject $command)
    {
        $project = $this->projectsFinder->findById($command->projectId()->toString());

        if (null === $project){
            throw NotFoundException::withMessage(sprintf(
                'Project with id: \'%s\' not found.', $command->projectId()->toString()
            ));
        }

        switch ($project['application']){
            case 'A07':

                break;
        }
    }
}
