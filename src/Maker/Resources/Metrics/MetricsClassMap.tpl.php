<?= "<?php\n"; ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

class MetricsClassMap
{
    public function getCollaboratorsClassNames()
    {
        return [
<?php foreach ($metrics_config as $entry): ?>
            <?= $entry['root_type']; ?>\<?= $entry['sub_type']; ?>\<?= $entry['metric_name']; ?>\Collaborator::class,
<?php endforeach; ?>
        ];
    }
}
