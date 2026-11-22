<?php

namespace App\Services;

use App\Models\Item;
use App\Models\ItemStatus;

class ReservedItemReleaseService
{
    public function release(?int $itemId): void
    {
        if (! $itemId) {
            return;
        }

        $item = Item::query()
            ->with('status')
            ->whereKey($itemId)
            ->lockForUpdate()
            ->first();

        if (
            ! $item
            || ! $item->is_active
            || ! in_array($item->status?->name, ['Reservado', 'Reservado para retiro'], true)
        ) {
            return;
        }

        $availableStatus = ItemStatus::firstOrCreate(['name' => 'Disponible']);
        $item->update(['item_status_id' => $availableStatus->id]);
    }
}
