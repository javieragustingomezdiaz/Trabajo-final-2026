<?php

namespace App\Services;

use App\Models\Item;
use App\Models\Material;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;

class ItemCatalogService
{
    public function store(Material $material, array $data): Item
    {
        return DB::transaction(function () use ($material, $data): Item {
            // The temporary value is replaced before the transaction commits.
            $item = Item::query()->create([
                'barcode' => 'TMP-' . strtoupper((string) Str::ulid()),
                'material_id' => $material->id,
                'item_status_id' => $data['item_status_id'],
                'item_condition_id' => $data['item_condition_id'],
                'container_id' => $data['container_id'],
                'observation' => $data['observation'],
            ]);

            $serial = str_pad((string) $item->getKey(), 8, '0', STR_PAD_LEFT);
            $barcode = 'ARC-' . $serial;

            if (Item::query()
                ->where('barcode', $barcode)
                ->whereKeyNot($item->getKey())
                ->exists()) {
                throw ValidationException::withMessages([
                    'barcode' => "No se pudo asignar el código {$barcode} porque ya pertenece a otro ejemplar.",
                ]);
            }

            $item->update(['barcode' => $barcode]);

            return $item->refresh();
        });
    }

    public function update(Item $item, array $data): Item
    {
        return DB::transaction(function () use ($item, $data): Item {
            $item->update([
                'item_status_id' => $data['item_status_id'],
                'item_condition_id' => $data['item_condition_id'],
                'container_id' => $data['container_id'],
                'observation' => $data['observation'],
            ]);

            return $item->refresh();
        });
    }
}
