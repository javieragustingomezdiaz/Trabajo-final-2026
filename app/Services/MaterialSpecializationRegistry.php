<?php

namespace App\Services;

use App\Models\MaterialType;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class MaterialSpecializationRegistry
{
    private const SPECIALIZATIONS = [
        'bibliographic' => [
            'relation' => 'bibliographic',
            'fields' => ['is_illustrated', 'binding_type'],
            'booleans' => ['is_illustrated'],
            'rules' => [
                'is_illustrated' => ['sometimes', 'boolean'],
                'binding_type' => ['nullable', 'string', 'max:50'],
            ],
        ],
        'serial_publication' => [
            'relation' => 'serialpublication',
            'fields' => [
                'frequency',
                'start_year',
                'end_year',
                'initial_numeric_designation',
            ],
            'booleans' => [],
            'rules' => [
                'frequency' => ['nullable', 'string', 'max:50'],
                'start_year' => ['nullable', 'integer'],
                'end_year' => ['nullable', 'integer'],
                'initial_numeric_designation' => ['nullable', 'string', 'max:50'],
            ],
        ],
        'cartographic' => [
            'relation' => 'cartographic',
            'fields' => ['scale', 'projection', 'coordinate', 'orientation'],
            'booleans' => [],
            'rules' => [
                'scale' => ['nullable', 'string', 'max:50'],
                'projection' => ['nullable', 'string', 'max:50'],
                'coordinate' => ['nullable', 'string', 'max:50'],
                'orientation' => ['nullable', 'string', 'max:50'],
            ],
        ],
        'graph' => [
            'relation' => 'graph',
            'fields' => ['technical', 'color'],
            'booleans' => [],
            'rules' => [
                'technical' => ['nullable', 'string', 'max:50'],
                'color' => ['nullable', 'string', 'max:50'],
            ],
        ],
        'musical' => [
            'relation' => 'musical',
            'fields' => ['musical_notation', 'performance_medium'],
            'booleans' => [],
            'rules' => [
                'musical_notation' => ['nullable', 'string', 'max:50'],
                'performance_medium' => ['nullable', 'string'],
            ],
        ],
        'sound' => [
            'relation' => 'sound',
            'fields' => [
                'duration_seconds',
                'recording_type',
                'audio_channel',
                'playback_speed',
                'number_tracks',
            ],
            'booleans' => [],
            'rules' => [
                'duration_seconds' => ['nullable', 'integer', 'min:0'],
                'recording_type' => ['nullable', 'string', 'max:50'],
                'audio_channel' => ['nullable', 'string', 'max:50'],
                'playback_speed' => ['nullable', 'string', 'max:50'],
                'number_tracks' => ['nullable', 'integer', 'min:0'],
            ],
        ],
        'video' => [
            'relation' => 'video',
            'fields' => ['duration_seconds', 'video_format', 'aspect_ratio'],
            'booleans' => [],
            'rules' => [
                'duration_seconds' => ['nullable', 'integer', 'min:0'],
                'video_format' => ['nullable', 'string', 'max:255'],
                'aspect_ratio' => ['nullable', 'string', 'max:255'],
            ],
        ],
        'object' => [
            'relation' => 'object',
            'fields' => [
                'number_pieces',
                'requires_assembly',
                'manufacter',
                'serial_number',
            ],
            'booleans' => ['requires_assembly'],
            'rules' => [
                'number_pieces' => ['sometimes', 'integer', 'min:1'],
                'requires_assembly' => ['sometimes', 'boolean'],
                'manufacter' => ['nullable', 'string', 'max:50'],
                'serial_number' => ['nullable', 'string', 'max:50'],
            ],
        ],
    ];

    public function forType(MaterialType $materialType): array
    {
        $group = Str::slug($materialType->group, '_');

        $group = match ($group) {
            'bibliographic_materials' => 'bibliographic',
            'serial_publications' => 'serial_publication',
            'cartographic_materials' => 'cartographic',
            'graph_materials' => 'graph',
            'musical_materials' => 'musical',
            'sound_materials' => 'sound',
            'video_materials' => 'video',
            'object_materials' => 'object',
            default => $group,
        };

        if (! isset(self::SPECIALIZATIONS[$group])) {
            throw ValidationException::withMessages([
                'material_type_id' => "El grupo '{$materialType->group}' no tiene una especialización configurada.",
            ]);
        }

        return self::SPECIALIZATIONS[$group];
    }
}