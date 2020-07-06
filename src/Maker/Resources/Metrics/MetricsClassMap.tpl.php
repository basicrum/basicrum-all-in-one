<?= "<?php\n"; ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

class MetricsClassMap
{
    public function getCollaboratorsClassNames()
    {
        return [
<?php foreach ($metrics_config as $entry): ?>
            <?= $entry['belongs_to']; ?>\<?= $entry['metric_name']; ?>\Collaborator::class,
<?php endforeach; ?>
        ];
    }
}
