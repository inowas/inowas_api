<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Infrastructure\Projection;

/**
 * Class Table
 */
final class Table
{
    const BOUNDARY_ACTIVE_CELLS = 'mf_projection_boundaries_active_cells';
    const BOUNDARY_LIST = 'mf_projection_boundaries';
    const BOUNDARY_OBSERVATION_POINT_VALUES = 'mf_projection_boundaries_observation_point_values';
    const CALCULATIONS   = 'mf_projection_calculations';
    const MODFLOWMODELS_LIST = 'mf_projection_modflowmodels';
    const MODFLOWMODEL_HASH = 'mf_projection_modflowmodel_hash';
    const PACKAGES_HASH = 'mf_projection_packages_hash';
}
