<?php

namespace Inowas\Common\Boundaries;


use Inowas\Common\Geometry\Geometry;
use Inowas\Common\Grid\AffectedLayers;
use Inowas\Common\Id\BoundaryId;
use Inowas\ModflowBundle\Exception\InvalidArgumentException;

class BoundaryFactory
{
    /** @noinspection MoreThanThreeArgumentsInspection
     * @param BoundaryType $type
     * @param BoundaryId $boundaryId
     * @param BoundaryName $name
     * @param Geometry $geometry
     * @param AffectedLayers $affectedLayers
     * @param Metadata $metadata
     * @return ModflowBoundary
     * @throws InvalidArgumentException
     */
    public static function create(
        BoundaryType $type,
        BoundaryId $boundaryId,
        BoundaryName $name,
        Geometry $geometry,
        AffectedLayers $affectedLayers,
        Metadata $metadata
    ): ModflowBoundary
    {

        switch ($type->toString()) {
            case BoundaryType::CONSTANT_HEAD:
                return ConstantHeadBoundary::createWithParams(
                    $boundaryId,
                    $name,
                    $geometry,
                    $affectedLayers,
                    $metadata
                );
                break;

            case BoundaryType::GENERAL_HEAD:
                return GeneralHeadBoundary::createWithParams(
                    $boundaryId,
                    $name,
                    $geometry,
                    $affectedLayers,
                    $metadata
                );
                break;

            case BoundaryType::RECHARGE:
                return RechargeBoundary::createWithParams(
                    $boundaryId,
                    $name,
                    $geometry,
                    $affectedLayers,
                    $metadata
                );
                break;

            case BoundaryType::RIVER:
                return RiverBoundary::createWithParams(
                    $boundaryId,
                    $name,
                    $geometry,
                    $affectedLayers,
                    $metadata
                );
                break;

            case BoundaryType::WELL:
                return WellBoundary::createWithParams(
                    $boundaryId,
                    $name,
                    $geometry,
                    $affectedLayers,
                    $metadata
                );
                break;
        }

        throw InvalidArgumentException::withMessage(
            sprintf('BoundaryType %s not known', $type->toString())
        );
    }
}
