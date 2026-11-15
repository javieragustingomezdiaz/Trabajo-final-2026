<?php

namespace App\Services;

use App\Models\Author;
use App\Models\Material;
use App\Models\MaterialType;
use App\Models\Topic;
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
            $materialData = $this->materialData($data, $specialization);
            $materialData['material_type_id'] = $materialType->id;

            $material = Material::create($materialData);
            $this->saveRelatedData($material, $data, $specialization);

            return $material;
        });
    }

    public function update(
        Material $material,
        array $data,
        array $specialization
    ): Material {
        return DB::transaction(function () use ($material, $data, $specialization): Material {
            $material->update($this->materialData($data, $specialization));
            $this->saveRelatedData($material, $data, $specialization);

            return $material->refresh();
        });
    }

    private function materialData(array $data, array $specialization): array
    {
        return Arr::except($data, [
            ...$specialization['fields'],
            'author_ids',
            'topic_ids',
            'new_authors',
            'new_topics',
        ]);
    }

    private function saveRelatedData(
        Material $material,
        array $data,
        array $specialization
    ): void {
        $authorIds = $data['author_ids'] ?? [];

        foreach ($data['new_authors'] ?? [] as $newAuthorData) {
            $author = Author::create([
                'name' => $newAuthorData['name'],
                'author_type_id' => $newAuthorData['author_type_id'],
                'birth_date' => $newAuthorData['birth_date'] ?? null,
                'death_date' => $newAuthorData['death_date'] ?? null,
            ]);

            $authorIds[] = $author->id;
        }

        $material->authors()->sync(array_values(array_unique($authorIds)));

        $topicIds = $data['topic_ids'] ?? [];

        foreach ($data['new_topics'] ?? [] as $newTopicData) {
            $topic = Topic::firstOrCreate([
                'name' => trim($newTopicData['name']),
            ]);

            $topicIds[] = $topic->id;
        }

        $material->topics()->sync(array_values(array_unique($topicIds)));

        $specializationData = Arr::only($data, $specialization['fields']);

        foreach ($specialization['booleans'] as $field) {
            $specializationData[$field] = (bool) ($data[$field] ?? false);
        }

        $material->{$specialization['relation']}()
            ->updateOrCreate([], $specializationData);
    }
}
