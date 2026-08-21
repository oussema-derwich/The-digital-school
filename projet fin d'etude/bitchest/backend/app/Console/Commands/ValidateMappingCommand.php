<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MappingService;
use Illuminate\Support\Str;

class ValidateMappingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mapping:validate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Valide toutes les configurations de mapping et signale les problèmes';

    protected $errors = [];
    protected $warnings = [];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Validation des Configurations de Mapping...');
        $this->newLine();

        $this->validateModels();
        $this->validateControllers();
        $this->validateServices();
        $this->validateEvents();
        $this->validateJobs();
        $this->validateObservers();
        $this->validateRelations();

        $this->displayResults();

        return count($this->errors) > 0 ? 1 : 0;
    }

    /**
     * Valide les modèles
     */
    protected function validateModels(): void
    {
        $this->info('📦 Validation des modèles...');

        $models = config('mapping.models', []);
        
        foreach ($models as $key => $mapping) {
            // Vérifier que la classe existe
            if (!class_exists($mapping['class'])) {
                $this->errors[] = "Modèle: Classe {$mapping['class']} n'existe pas";
            }

            // Vérifier les relations
            if (isset($mapping['relations'])) {
                foreach ($mapping['relations'] as $relationName => $relationConfig) {
                    if (!class_exists($relationConfig['model'])) {
                        $this->errors[] = "Modèle: Classe de relation {$relationConfig['model']} n'existe pas (relation: {$relationName})";
                    }
                }
            }
        }

        $this->info("✓ " . count($models) . " modèles validés");
    }

    /**
     * Valide les contrôleurs
     */
    protected function validateControllers(): void
    {
        $this->info('🎮 Validation des contrôleurs...');

        $controllers = config('mapping.controllers', []);
        
        foreach ($controllers as $key => $config) {
            if (!class_exists($config['class'])) {
                $this->errors[] = "Contrôleur: Classe {$config['class']} n'existe pas";
            }
        }

        $this->info("✓ " . count($controllers) . " contrôleurs validés");
    }

    /**
     * Valide les services
     */
    protected function validateServices(): void
    {
        $this->info('⚙️ Validation des services...');

        $services = config('services-config.services', []);
        
        foreach ($services as $key => $config) {
            if (!class_exists($config['class'])) {
                $this->errors[] = "Service: Classe {$config['class']} n'existe pas";
            }
        }

        $this->info("✓ " . count($services) . " services validés");
    }

    /**
     * Valide les événements
     */
    protected function validateEvents(): void
    {
        $this->info('📡 Validation des événements...');

        $events = config('services-config.events', []);
        
        foreach ($events as $key => $config) {
            if (!class_exists($config['class'])) {
                $this->errors[] = "Événement: Classe {$config['class']} n'existe pas";
            }

            if (isset($config['listeners'])) {
                foreach ($config['listeners'] as $listener) {
                    if (!class_exists($listener)) {
                        $this->errors[] = "Listener: Classe {$listener} n'existe pas (événement: {$key})";
                    }
                }
            }
        }

        $this->info("✓ " . count($events) . " événements validés");
    }

    /**
     * Valide les jobs
     */
    protected function validateJobs(): void
    {
        $this->info('📅 Validation des jobs...');

        $jobs = config('services-config.jobs', []);
        
        foreach ($jobs as $key => $config) {
            if (!class_exists($config['class'])) {
                $this->errors[] = "Job: Classe {$config['class']} n'existe pas";
            }

            if (!isset($config['schedule'])) {
                $this->warnings[] = "Job: Pas de schedule défini pour {$key}";
            }

            if (!isset($config['timeout'])) {
                $this->warnings[] = "Job: Pas de timeout défini pour {$key}";
            }
        }

        $this->info("✓ " . count($jobs) . " jobs validés");
    }

    /**
     * Valide les observateurs
     */
    protected function validateObservers(): void
    {
        $this->info('👁️ Validation des observateurs...');

        $observers = config('services-config.observers', []);
        
        foreach ($observers as $model => $observer) {
            $modelClass = "App\\Models\\{$model}";
            
            if (!class_exists($modelClass)) {
                $this->errors[] = "Observateur: Modèle {$modelClass} n'existe pas";
            }

            if (!class_exists($observer)) {
                $this->errors[] = "Observateur: Classe {$observer} n'existe pas";
            }
        }

        $this->info("✓ " . count($observers) . " observateurs validés");
    }

    /**
     * Valide les relations
     */
    protected function validateRelations(): void
    {
        $this->info('🔗 Validation des relations...');

        $models = config('mapping.models', []);
        $relationCount = 0;

        foreach ($models as $key => $mapping) {
            if (isset($mapping['relations'])) {
                $relationCount += count($mapping['relations']);
            }
        }

        $this->info("✓ {$relationCount} relations validées");
    }

    /**
     * Affiche les résultats
     */
    protected function displayResults(): void
    {
        $this->newLine(2);

        if (count($this->errors) === 0 && count($this->warnings) === 0) {
            $this->info('✅ Toutes les configurations sont valides!');
            return;
        }

        if (count($this->warnings) > 0) {
            $this->warn("⚠️ Avertissements (" . count($this->warnings) . "):");
            foreach ($this->warnings as $warning) {
                $this->line("  • {$warning}");
            }
            $this->newLine();
        }

        if (count($this->errors) > 0) {
            $this->error("❌ Erreurs (" . count($this->errors) . "):");
            foreach ($this->errors as $error) {
                $this->line("  • {$error}");
            }
            $this->newLine();
        }

        // Résumé
        $this->info('📊 Résumé:');
        $this->line("  Erreurs: " . count($this->errors));
        $this->line("  Avertissements: " . count($this->warnings));
        $this->line("  Statut: " . (count($this->errors) === 0 ? '✅ OK' : '❌ ERREURS'));
    }
}
