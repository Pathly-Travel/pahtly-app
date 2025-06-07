<?php

namespace Src\Support\Traits;

trait HasDomainData
{
    /**
     * Transform model to domain data object
     */
    public function toDomainData(): mixed
    {
        if (property_exists($this, 'dataClass')) {
            $dataClass = $this->dataClass;
            return $dataClass::from($this->toArray());
        }
        
        return $this->toArray();
    }
} 