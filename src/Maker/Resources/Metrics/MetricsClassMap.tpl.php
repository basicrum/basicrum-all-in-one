<?= "<?php\n"; ?>

declare(strict_types=1);

namespace <?= $namespace; ?>;

class MetricsClassMap
{

    public function getCollaboratorsClassNames()
    {
        return [
<?php foreach ($metric_names as $name): ?>
            TechnicalMetrics\<?= $name; ?>\Collaborator::class,
<?php endforeach; ?>
        ];
    }

}
