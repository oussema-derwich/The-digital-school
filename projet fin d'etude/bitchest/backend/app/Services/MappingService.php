<?php

namespace App\Services;

use Illuminate\Support\Str;

/**
 * Service de Mapping - Gère l'application des configurations centralisées
 * 
 * Utilise les fichiers de configuration pour injecter automatiquement
 * les relations, validations, et transformations dans les modèles
 */
class MappingService
{
    /**
     * Obtient la configuration complète de mapping
     */
    public static function getAllMappings()
    {
        return config('mapping');
    }

    /**
     * Obtient la configuration du mapping pour un modèle spécifique
     */
    public static function getModelMapping($modelName)
    {
        $key = strtolower(Str::singular($modelName));
        return config("mapping.models.{$key}");
    }

    /**
     * Obtient les relations d'un modèle
     */
    public static function getModelRelations($modelName)
    {
        $mapping = self::getModelMapping($modelName);
        return $mapping['relations'] ?? [];
    }

    /**
     * Obtient les validations pour une action
     */
    public static function getValidationRules($modelName, $action = null)
    {
        $key = strtolower(Str::singular($modelName));
        
        if ($action) {
            return config("mapping.validations.{$key}.{$action}");
        }
        
        return config("mapping.validations.{$key}");
    }

    /**
     * Obtient la transformation pour un modèle
     */
    public static function getTransformation($modelName, $type = 'default')
    {
        $key = strtolower(Str::singular($modelName));
        return config("mapping.transformations.{$key}.{$type}", []);
    }

    /**
     * Obtient la configuration d'un service
     */
    public static function getServiceConfig($serviceName)
    {
        return config("services-config.services.{$serviceName}");
    }

    /**
     * Obtient la liste des événements
     */
    public static function getEvents()
    {
        return config('services-config.events');
    }

    /**
     * Obtient les listeners pour un événement
     */
    public static function getEventListeners($eventName)
    {
        return config("services-config.events.{$eventName}.listeners", []);
    }

    /**
     * Obtient la configuration des jobs
     */
    public static function getJobs()
    {
        return config('services-config.jobs');
    }

    /**
     * Obtient la configuration d'un job spécifique
     */
    public static function getJobConfig($jobName)
    {
        return config("services-config.jobs.{$jobName}");
    }

    /**
     * Obtient les observateurs
     */
    public static function getObservers()
    {
        return config('services-config.observers');
    }

    /**
     * Obtient la configuration de cache pour un type
     */
    public static function getCacheConfig($type)
    {
        return config("services-config.caches.{$type}");
    }

    /**
     * Obtient les permissions pour un rôle
     */
    public static function getPermissions($role)
    {
        return config("services-config.permissions.{$role}", []);
    }

    /**
     * Vérifie si une permission existe pour un rôle
     */
    public static function hasPermission($role, $permission)
    {
        $permissions = self::getPermissions($role);
        return isset($permissions[$permission]) && $permissions[$permission] === true;
    }

    /**
     * Obtient la configuration des notifications
     */
    public static function getNotificationConfig($type)
    {
        return config("services-config.notification_types.{$type}");
    }

    /**
     * Obtient la configuration d'une API externe
     */
    public static function getExternalApiConfig($apiName)
    {
        return config("services-config.external_apis.{$apiName}");
    }

    /**
     * Génère un rapport de mapping
     */
    public static function generateMappingReport()
    {
        $report = [
            'models' => array_keys(config('mapping.models', [])),
            'controllers' => array_keys(config('mapping.controllers', [])),
            'services' => array_keys(config('services-config.services', [])),
            'events' => array_keys(config('services-config.events', [])),
            'jobs' => array_keys(config('services-config.jobs', [])),
            'observers' => array_keys(config('services-config.observers', [])),
        ];

        return $report;
    }
}
