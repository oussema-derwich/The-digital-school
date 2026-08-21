<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\MappingService;

/**
 * Service Provider pour les Mappings
 * 
 * Enregistre et initialise tous les mappings centralisés
 */
class MappingServiceProvider extends ServiceProvider
{
    /**
     * Enregistre les services de mapping
     */
    public function register(): void
    {
        // Enregistre le service de mapping dans le conteneur
        $this->app->singleton('mapping', function ($app) {
            return new MappingService();
        });

        // Enregistre les configurations
        $this->registerConfigurations();
    }

    /**
     * Bootstrap les services
     */
    public function boot(): void
    {
        // Applique les mappings aux modèles
        $this->bootModelRelations();

        // Enregistre les observateurs
        $this->registerObservers();

        // Enregistre les événements
        $this->registerEvents();

        // Publie les configurations
        $this->publishConfigs();
    }

    /**
     * Enregistre les configurations
     */
    protected function registerConfigurations(): void
    {
        $configPath = config_path('mapping.php');
        $servicesPath = config_path('services-config.php');

        if (file_exists($configPath)) {
            $this->mergeConfigFrom($configPath, 'mapping');
        }

        if (file_exists($servicesPath)) {
            $this->mergeConfigFrom($servicesPath, 'services-config');
        }
    }

    /**
     * Bootstrap les relations des modèles
     */
    protected function bootModelRelations(): void
    {
        // Les relations sont définies dans les modèles eux-mêmes
        // Cette méthode peut être étendue pour appliquer des relations dynamiques
    }

    /**
     * Enregistre les observateurs
     */
    protected function registerObservers(): void
    {
        $observers = config('services-config.observers', []);

        foreach ($observers as $model => $observer) {
            $modelClass = "App\\Models\\{$model}";
            if (class_exists($modelClass) && class_exists($observer)) {
                $modelClass::observe($observer);
            }
        }
    }

    /**
     * Enregistre les événements et leurs listeners
     */
    protected function registerEvents(): void
    {
        $events = config('services-config.events', []);

        foreach ($events as $eventName => $eventConfig) {
            if (isset($eventConfig['listeners'])) {
                foreach ($eventConfig['listeners'] as $listener) {
                    $this->app['events']->listen(
                        $eventConfig['class'],
                        $listener
                    );
                }
            }
        }
    }

    /**
     * Publie les configurations
     */
    protected function publishConfigs(): void
    {
        $this->publishes([
            __DIR__ . '/../config/mapping.php' => config_path('mapping.php'),
            __DIR__ . '/../config/services-config.php' => config_path('services-config.php'),
        ], 'bitchest-config');
    }
}
