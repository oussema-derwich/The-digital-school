<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MappingService;

class MappingReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mapping:report {--format=table}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Affiche un rapport complet des configurations de mapping';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $format = $this->option('format');

        $this->info('📋 Rapport de Mapping - ' . now()->format('Y-m-d H:i:s'));
        $this->newLine();

        // Rapport général
        $report = MappingService::generateMappingReport();
        
        if ($format === 'json') {
            $this->line(json_encode($report, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            return;
        }

        // Modèles
        $this->showModelsReport($report);
        $this->newLine();

        // Contrôleurs
        $this->showControllersReport($report);
        $this->newLine();

        // Services
        $this->showServicesReport($report);
        $this->newLine();

        // Événements
        $this->showEventsReport($report);
        $this->newLine();

        // Jobs
        $this->showJobsReport($report);
        $this->newLine();

        // Observateurs
        $this->showObserversReport($report);
        $this->newLine();

        $this->info('✅ Rapport complet généré avec succès!');
    }

    /**
     * Affiche le rapport des modèles
     */
    protected function showModelsReport($report): void
    {
        $this->line('');
        $this->line('📦 Modèles (' . count($report['models']) . ')');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        
        $headers = ['Modèle', 'Relations', 'Attributs'];
        $rows = [];

        foreach ($report['models'] as $model) {
            $mapping = MappingService::getModelMapping($model);
            if ($mapping) {
                $relations = count($mapping['relations'] ?? []);
                $attributes = count($mapping['attributes']['fillable'] ?? []);
                $rows[] = [
                    $model,
                    $relations,
                    $attributes,
                ];
            }
        }

        if (!empty($rows)) {
            $this->table($headers, $rows);
        } else {
            $this->warn('Aucun modèle configuré');
        }
    }

    /**
     * Affiche le rapport des contrôleurs
     */
    protected function showControllersReport($report): void
    {
        $this->line('');
        $this->line('🎮 Contrôleurs (' . count($report['controllers']) . ')');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        
        $headers = ['Contrôleur', 'Classe', 'Actions'];
        $rows = [];

        $controllers = config('mapping.controllers', []);
        foreach ($report['controllers'] as $controller) {
            if (isset($controllers[$controller])) {
                $config = $controllers[$controller];
                $actions = count($config['actions'] ?? []);
                $rows[] = [
                    $controller,
                    class_basename($config['class']),
                    $actions,
                ];
            }
        }

        if (!empty($rows)) {
            $this->table($headers, $rows);
        } else {
            $this->warn('Aucun contrôleur configuré');
        }
    }

    /**
     * Affiche le rapport des services
     */
    protected function showServicesReport($report): void
    {
        $this->line('');
        $this->line('⚙️ Services (' . count($report['services']) . ')');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        
        $headers = ['Service', 'Classe', 'Méthodes'];
        $rows = [];

        $services = config('services-config.services', []);
        foreach ($report['services'] as $service) {
            if (isset($services[$service])) {
                $config = $services[$service];
                $methods = count($config['methods'] ?? []);
                $rows[] = [
                    $service,
                    class_basename($config['class']),
                    $methods,
                ];
            }
        }

        if (!empty($rows)) {
            $this->table($headers, $rows);
        } else {
            $this->warn('Aucun service configuré');
        }
    }

    /**
     * Affiche le rapport des événements
     */
    protected function showEventsReport($report): void
    {
        $this->line('');
        $this->line('📡 Événements (' . count($report['events']) . ')');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        
        $headers = ['Événement', 'Classe', 'Listeners'];
        $rows = [];

        $events = config('services-config.events', []);
        foreach ($report['events'] as $event) {
            if (isset($events[$event])) {
                $config = $events[$event];
                $listeners = count($config['listeners'] ?? []);
                $rows[] = [
                    $event,
                    class_basename($config['class']),
                    $listeners,
                ];
            }
        }

        if (!empty($rows)) {
            $this->table($headers, $rows);
        } else {
            $this->warn('Aucun événement configuré');
        }
    }

    /**
     * Affiche le rapport des jobs
     */
    protected function showJobsReport($report): void
    {
        $this->line('');
        $this->line('📅 Jobs Planifiés (' . count($report['jobs']) . ')');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        
        $headers = ['Job', 'Classe', 'Schedule', 'Timeout'];
        $rows = [];

        $jobs = config('services-config.jobs', []);
        foreach (array_keys($jobs) as $job) {
            if (isset($jobs[$job])) {
                $config = $jobs[$job];
                $rows[] = [
                    $job,
                    class_basename($config['class']),
                    $config['schedule'] ?? '-',
                    $config['timeout'] ?? '-',
                ];
            }
        }

        if (!empty($rows)) {
            $this->table($headers, $rows);
        } else {
            $this->warn('Aucun job configuré');
        }
    }

    /**
     * Affiche le rapport des observateurs
     */
    protected function showObserversReport($report): void
    {
        $this->line('');
        $this->line('👁️ Observateurs (' . count($report['observers']) . ')');
        $this->line('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        
        $headers = ['Modèle', 'Observateur'];
        $rows = [];

        $observers = config('services-config.observers', []);
        foreach ($observers as $model => $observer) {
            $rows[] = [
                $model,
                class_basename($observer),
            ];
        }

        if (!empty($rows)) {
            $this->table($headers, $rows);
        } else {
            $this->warn('Aucun observateur configuré');
        }
    }
}
