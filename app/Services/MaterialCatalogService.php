<?php

namespace App\Services;

use App\Models\Material;
use App\Models\MaterialType;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class MaterialCatalogService
{
    public function store(
        array $data,
        MaterialType $materialType,
        array $specialization
    ): Material {
        return DB::transaction(function () use ($data, $materialType, $specialization): Material {
            $materialData = Arr::except($data, $specialization['fields']);
            $materialData['material_type_id'] = $materialType->id;

            $material = Material::create($materialData);

            $specializationData = Arr::only(
                $data,
                $specialization['fields']
            );

            foreach ($specialization['booleans'] as $field) {
                $specializationData[$field] = (bool) ($data[$field] ?? false);
            }

            $material->{$specialization['relation']}()
                ->create($specializationData);

            return $material;
        });
    }
}