<?php

/**
 * Helpers pour les Mappings
 * 
 * Fonctions utilitaires pour accéder rapidement aux configurations de mapping
 */

if (!function_exists('mapping')) {
    /**
     * Retourne une instance du service de mapping
     */
    function mapping()
    {
        return app('mapping');
    }
}

if (!function_exists('model_mapping')) {
    /**
     * Retourne le mapping pour un modèle
     */
    function model_mapping($modelName)
    {
        return mapping()->getModelMapping($modelName);
    }
}

if (!function_exists('model_relations')) {
    /**
     * Retourne les relations d'un modèle
     */
    function model_relations($modelName)
    {
        return mapping()->getModelRelations($modelName);
    }
}

if (!function_exists('validation_rules')) {
    /**
     * Retourne les règles de validation
     */
    function validation_rules($modelName, $action = null)
    {
        return mapping()->getValidationRules($modelName, $action);
    }
}

if (!function_exists('transform_model')) {
    /**
     * Retourne la transformation pour un modèle
     */
    function transform_model($modelName, $type = 'default')
    {
        return mapping()->getTransformation($modelName, $type);
    }
}

if (!function_exists('service_config')) {
    /**
     * Retourne la configuration d'un service
     */
    function service_config($serviceName)
    {
        return mapping()->getServiceConfig($serviceName);
    }
}

if (!function_exists('has_permission')) {
    /**
     * Vérifie si un rôle a une permission
     */
    function has_permission($role, $permission)
    {
        return mapping()->hasPermission($role, $permission);
    }
}

if (!function_exists('cache_config')) {
    /**
     * Retourne la configuration de cache
     */
    function cache_config($type)
    {
        return mapping()->getCacheConfig($type);
    }
}

if (!function_exists('notification_config')) {
    /**
     * Retourne la configuration des notifications
     */
    function notification_config($type)
    {
        return mapping()->getNotificationConfig($type);
    }
}

if (!function_exists('external_api')) {
    /**
     * Retourne la configuration d'une API externe
     */
    function external_api($apiName)
    {
        return mapping()->getExternalApiConfig($apiName);
    }
}

if (!function_exists('mapping_report')) {
    /**
     * Génère un rapport de mapping
     */
    function mapping_report()
    {
        return mapping()->generateMappingReport();
    }
}
