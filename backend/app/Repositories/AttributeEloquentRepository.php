<?php

namespace App\Repositories;

use App\Models\Attribute;

class AttributeEloquentRepository extends EloquentRepository
{
    public function getModel()
    {
        return Attribute::class;
    }

}