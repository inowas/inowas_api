<?php

declare(strict_types=1);

namespace Inowas\ScenarioAnalysis\Model\Exception;

use Inowas\ScenarioAnalysis\Model\ScenarioAnalysisId;

final class ScenarioAnalysisNotFoundException extends \InvalidArgumentException
{
    public static function withId(ScenarioAnalysisId $id)
    {
        return new self(sprintf('ScenarioAnalysis with id %s cannot be found.', $id->toString()));
    }
}
