<?php

namespace App\Services;

use App\Models\Author;
use App\Models\Material;
use App\Models\MaterialType;
use App\Models\Topic;
use Illuminate\Support\Arr;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;

class MaterialCatalogService
{
    public function store(
        array $data,
        MaterialType $materialType,
        array $specialization
    ): Material {
        $coverPath = $this->storeCover($data['cover'] ?? null);

        try {
            return DB::transaction(function () use ($data, $materialType, $specialization, $coverPath): Material {
                $materialData = $this->materialData($data, $specialization);
                $materialData['material_type_id'] = $materialType->id;
                $materialData['cover'] = $coverPath;

                $material = Material::create($materialData);
                $this->saveRelatedData($material, $data, $specialization);

                return $material;
            });
        } catch (Throwable $exception) {
            if ($coverPath) {
                Storage::disk('public')->delete($coverPath);
            }

            throw $exception;
        }
    }

    public function update(
        Material $material,
        array $data,
        array $specialization
    ): Material {
        $newCoverPath = $this->storeCover($data['cover'] ?? null);
        $removeCover = ! $newCoverPath && (bool) ($data['remove_cover'] ?? false);
        $oldCoverPath = $material->cover;

        try {
            $updatedMaterial = DB::transaction(function () use ($material, $data, $specialization, $newCoverPath, $removeCover): Material {
                $materialData = $this->materialData($data, $specialization);

                if ($newCoverPath) {
                    $materialData['cover'] = $newCoverPath;
                } elseif ($removeCover) {
                    $materialData['cover'] = null;
                }

                $material->update($materialData);
                $this->saveRelatedData($material, $data, $specialization);

                return $material->refresh();
            });
        } catch (Throwable $exception) {
            if ($newCoverPath) {
                Storage::disk('public')->delete($newCoverPath);
            }

            throw $exception;
        }

        if (($newCoverPath || $removeCover) && $oldCoverPath) {
            $this->deleteStoredCover($oldCoverPath);
        }

        return $updatedMaterial;
    }

    private function materialData(array $data, array $specialization): array
    {
        return Arr::except($data, [
            ...$specialization['fields'],
            'cover',
            'remove_cover',
            'author_ids',
            'topic_ids',
            'new_authors',
            'new_topics',
        ]);
    }

    private function storeCover(?UploadedFile $cover): ?string
    {
        if (! $cover) {
            return null;
        }

        $path = $cover->store('covers', 'public');

        if (! is_string($path)) {
            throw new RuntimeException('No se pudo guardar la portada.');
        }

        return $path;
    }

    private function deleteStoredCover(string $path): void
    {
        if (Str::startsWith($path, 'covers/')) {
            Storage::disk('public')->delete($path);
        }
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
