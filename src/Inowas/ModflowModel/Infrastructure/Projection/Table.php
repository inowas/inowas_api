<?php

declare(strict_types=1);

namespace Inowas\ModflowModel\Infrastructure\Projection;

/**
 * Class Table
 */
final class Table
{
    public const CALCULATIONS   = 'mf_projection_calculations';
    public const CALCULATION_PROCESSES   = 'mf_projection_calculation_processes';
    public const BOUNDARIES = 'mf_projection_boundaries';
    public const MODFLOWMODELS = 'mf_projection_modflowmodels';
    public const MODFLOWMODEL_HASH = 'mf_projection_modflowmodel_hash';
    public const OPTIMIZATIONS = 'mf_projection_optimizations';
    public const OPTIMIZATION_PROCESSES = 'mf_projection_optimization_processes';
    public const PACKAGES_HASH = 'mf_projection_packages_hash';
    public const SOILMODELS = 'mf_projection_soilmodels';
    public const SOILMODEL_LAYERS_LIST = 'mf_projection_soilmodel_layers';
}
