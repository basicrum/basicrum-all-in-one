<?php

declare(strict_types=1);

namespace App\BasicRum\Visit\Calculator\Aggregator;

class Chunk
{
    /** @var Completed */
    private $completed;

    public function __construct()
    {
        $this->completed = new Completed();
    }

    public function chunkenize(array $views, int $duration): array
    {
        $chunks = [];

        $current = reset($views);
        $lastScanned = $current;
        $next = next($views);

        while (true) {
            if (false === $next) {
                $chunks[] = [
                    'begin' => $current['rumDataId'],
                    'end' => end($views)['rumDataId'],
                ];

                break;
            }

            if ($this->completed->isVisitCompleted($current['createdAt'], $next['createdAt'], $duration)) {
                $chunks[] = [
                    'begin' => $current['rumDataId'],
                    'end' => $lastScanned['rumDataId'],
                ];

                $current = $next;
            }

            $lastScanned = $next;
            $next = next($views);
        }

        return $chunks;
    }
}
