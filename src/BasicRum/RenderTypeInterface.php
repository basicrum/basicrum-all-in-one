<?php

namespace App\BasicRum;

interface RenderTypeInterface
{
    public function build(DiagramOrchestrator $diagramOrchestrator, array $params, Release $releaseRepository): array;
}
