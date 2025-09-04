<?php

namespace App\Repositories;

use App\Models\AttributeValue;

class AttributeValueEloquentRepository extends EloquentRepository
{
    public function getModel()
    {
        return AttributeValue::class;
    }

}