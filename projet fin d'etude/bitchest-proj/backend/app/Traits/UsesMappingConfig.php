<?php

namespace App\Traits;

/**
 * Trait pour appliquer les mappings centralisés à tous les modèles
 * 
 * Utilise les configurations du mapping.php pour appliquer automatiquement
 * les relations, les castings et autres configurations
 */

trait UsesMappingConfig
{
    /**
     * Initialise les relations basées sur la configuration
     */
    public function initializeRelations()
    {
        $config = config('mapping.models.' . strtolower(class_basename($this)));
        
        if ($config && isset($config['relations'])) {
            foreach ($config['relations'] as $relationName => $relationConfig) {
                $this->defineDynamicRelation($relationName, $relationConfig);
            }
        }
    }

    /**
     * Applique les castings basés sur la configuration
     */
    protected function applyConfigCasts()
    {
        $config = config('mapping.models.' . strtolower(class_basename($this)));
        
        if ($config && isset($config['attributes']['casts'])) {
            $this->casts = array_merge($this->casts ?? [], $config['attributes']['casts']);
        }
    }

    /**
     * Applique les fillables basés sur la configuration
     */
    protected function applyConfigFillable()
    {
        $config = config('mapping.models.' . strtolower(class_basename($this)));
        
        if ($config && isset($config['attributes']['fillable'])) {
            $this->fillable = array_merge($this->fillable ?? [], $config['attributes']['fillable']);
        }
    }

    /**
     * Obtient la configuration du modèle
     */
    public function getModelConfig()
    {
        return config('mapping.models.' . strtolower(class_basename($this)));
    }

    /**
     * Obtient les relations configurées
     */
    public function getConfiguredRelations()
    {
        $config = $this->getModelConfig();
        return $config['relations'] ?? [];
    }
}
