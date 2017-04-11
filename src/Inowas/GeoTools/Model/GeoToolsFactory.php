<?php

declare(strict_types=1);

namespace Inowas\GeoTools\Model;

use Doctrine\ORM\EntityManager;
use Inowas\GeoToolsBundle\Model\GeoTools;

class GeoToolsFactory
{

    /** @var EntityManager $entityManager */
    protected $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function create(): GeoTools
    {
        if (\geoPHP::geosInstalled()){
            return new GeosGeoTools();
        }

        return new PostGisGeoTools($this->entityManager);
    }
}
