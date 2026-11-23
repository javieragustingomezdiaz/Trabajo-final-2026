<?php

namespace App\Services;

use App\Models\Circulation;
use App\Models\LoanStatus;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CirculationSearchService
{
    public function __construct(private readonly CirculationRenewalService $renewalService)
    {
    }

    public function statuses(): Collection
    {
        return LoanStatus::query()->orderBy('name')->get();
    }

    public function search(array $filters): LengthAwarePaginator
    {
        $circulations = Circulation::query()
            ->with(['user.person', 'item.material.materialType.loanTerm', 'item.status', 'status'])
            ->when($filters['fecha_desde'] ?? null, fn ($query, $date) =>
                $query->whereDate('loan_date', '>=', $date))
            ->when($filters['fecha_hasta'] ?? null, fn ($query, $date) =>
                $query->whereDate('loan_date', '<=', $date))
            ->when(($filters['socio'] ?? '') !== '', function ($query) use ($filters): void {
                $term = $filters['socio'];

                $query->whereHas('user.person', fn ($personQuery) => $personQuery
                    ->where(function ($nameQuery) use ($term): void {
                        $nameQuery->where('name', 'like', "%{$term}%")
                            ->orWhere('last_name', 'like', "%{$term}%")
                            ->orWhereRaw("CONCAT(name, ' ', last_name) LIKE ?", ["%{$term}%"]);
                    }));
            })
            ->when(($filters['titulo'] ?? '') !== '', fn ($query) => $query
                ->whereHas('item.material', fn ($materialQuery) => $materialQuery
                    ->where('title', 'like', '%' . $filters['titulo'] . '%')))
            ->when($filters['estado_id'] ?? null, fn ($query, $statusId) =>
                $query->where('loan_status_id', $statusId))
            ->orderByDesc('loan_date')
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $circulations->getCollection()->each(function (Circulation $circulation): void {
            $circulation->setAttribute('can_renew', $this->renewalService->canRenew($circulation));
        });

        return $circulations;
    }
}
