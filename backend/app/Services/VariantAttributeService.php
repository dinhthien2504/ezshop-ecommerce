<?php

namespace App\Services;

class VariantAttributeService {
    
    public static function getFullNameVariant($variantAttributes)
    {
        return $variantAttributes
            ->unique(fn($va) => $va->attributeValue->attribute->id)
            ->sortBy(fn($va) => $va->attributeValue->attribute->name)
            ->map(function ($va) {
                $attribute = $va->attributeValue->attribute;
                return "{$attribute->name}: {$va->attributeValue->value}";
            })->implode(', ');
    }

}