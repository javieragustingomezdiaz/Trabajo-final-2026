<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\ReservationStatus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class ReservationSearchService
{
    public function statuses(): Collection
    {
        return ReservationStatus::query()->orderBy('name')->get();
    }

    public function search(array $filters): LengthAwarePaginator
    {
        $query = Reservation::query()
            ->with(['user.person', 'material', 'item', 'status'])
            ->when($filters['fecha_desde'] ?? null, fn ($query, $date) => $query->whereDate('registration_date', '>=', $date))
            ->when($filters['fecha_hasta'] ?? null, fn ($query, $date) => $query->whereDate('registration_date', '<=', $date))
            ->when(($filters['socio'] ?? '') !== '', function ($query) use ($filters): void {
                $term = $filters['socio'];

                $query->whereHas('user.person', fn ($personQuery) => $personQuery
                    ->where(function ($nameQuery) use ($term): void {
                        $nameQuery->where('name', 'like', "%{$term}%")
                            ->orWhere('last_name', 'like', "%{$term}%")
                            ->orWhereRaw("CONCAT(name, ' ', last_name) LIKE ?", ["%{$term}%"]);
                    }));
            })
            ->when(($filters['codigo_barras'] ?? '') !== '', fn ($query) => $query
                ->whereHas('item', fn ($itemQuery) => $itemQuery
                    ->where('barcode', 'like', '%' . $filters['codigo_barras'] . '%')))
            ->when(($filters['tipo'] ?? null) === 'inmediata', fn ($query) => $query
                ->whereNotNull('item_id')
                ->whereNull('notified_at'))
            ->when(($filters['tipo'] ?? null) === 'cola', fn ($query) => $query
                ->where(fn ($typeQuery) => $typeQuery
                    ->whereNull('item_id')
                    ->orWhereNotNull('notified_at')))
            ->when($filters['estado_id'] ?? null, fn ($query, $statusId) => $query
                ->where('reservation_status_id', $statusId));

        $scope = $filters['alcance'] ?? null;

        if ($scope === 'activas' || ($scope === null && ! ($filters['estado_id'] ?? null))) {
            $query->whereHas('status', fn ($statusQuery) => $statusQuery->whereIn('name', Reservation::ACTIVE_STATUSES));
        } elseif ($scope === 'historial') {
            $query->whereHas('status', fn ($statusQuery) => $statusQuery->whereNotIn('name', Reservation::ACTIVE_STATUSES));
        }

        return $query->orderByDesc('registration_date')
            ->paginate(15)->withQueryString();
    }
}
