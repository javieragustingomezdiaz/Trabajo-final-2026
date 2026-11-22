<?php

namespace App\Services;

use App\Models\LoanTerm;
use App\Models\MaterialType;

class LoanTermService
{
    public function saveForMaterialType(MaterialType $materialType, array $attributes): LoanTerm
    {
        return LoanTerm::query()->updateOrCreate(
            ['material_type_id' => $materialType->id],
            $attributes,
        );
    }
}
