<?php

declare(strict_types=1);

namespace App\BasicRum;

use App\BasicRum\Diagram\Builder\RenderType\RenderTypeFactory;

class DiagramBuilder
{
    public function build(DiagramOrchestrator $diagramOrchestrator, array $params, Release $releaseRepository): array
    {
        $renderType = $params['global']['presentation']['render_type'];

        $render = RenderTypeFactory::build($renderType, $diagramOrchestrator, $params, $releaseRepository);

        return $render->build();
    }
}
