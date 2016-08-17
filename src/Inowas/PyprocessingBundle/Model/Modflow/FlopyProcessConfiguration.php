<?php

namespace Inowas\PyprocessingBundle\Model\Modflow;

use Inowas\PyprocessingBundle\Model\PythonProcess\PythonProcessConfiguration;

class FlopyProcessConfiguration extends PythonProcessConfiguration
{
    /** @var  string */
    protected $baseUrl;

    /** @var string */
    protected $dataFolder;

    /** @var string */
    protected $modelId;

    /** @var string */
    protected $apiKey;

    /**
     * FlopyProcessConfiguration constructor.
     * @param $baseUrl
     * @param $dataFolder
     * @param $modelId
     * @param $apiKey
     */
    public function __construct($baseUrl, $dataFolder, $modelId, $apiKey)
    {
        $this->baseUrl = $baseUrl;
        $this->dataFolder = $dataFolder;
        $this->modelId = $modelId;
        $this->apiKey = $apiKey;

        $this->ignoreWarnings = true;
        $this->scriptName = 'flopy/FlopyCalculation.py';
    }

    /**
     * @return array
     */
    public function getArguments()
    {
        $this->arguments = parent::getArguments();
        $this->arguments[] = $this->baseUrl;
        $this->arguments[] = $this->dataFolder;
        $this->arguments[] = $this->modelId;
        $this->arguments[] = $this->apiKey;

        return $this->arguments;
    }
}