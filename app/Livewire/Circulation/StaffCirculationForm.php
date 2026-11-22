<?php

namespace App\Livewire\Circulation;

use App\Models\Circulation;
use App\Models\Item;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Locked;
use Livewire\Component;

class StaffCirculationForm extends Component
{
    public string $memberSearch = '';
    public string $itemSearch = '';
    public string $loanType = '';

    #[Locked]
    public ?int $selectedMemberId = null;

    #[Locked]
    public ?int $selectedItemId = null;

    public function boot(): void
    {
        Gate::authorize('create', Circulation::class);
    }

    public function mount(): void
    {
        $this->selectedMemberId = old('member_id') ? (int) old('member_id') : null;
        $this->selectedItemId = old('item_id') ? (int) old('item_id') : null;
        $this->loanType = old('loan_type', '');
    }

    public function selectMember(int $memberId): void
    {
        $this->eligibleMembers()->whereKey($memberId)->firstOrFail();

        $this->selectedMemberId = $memberId;
        $this->selectedItemId = null;
        $this->loanType = '';
        $this->memberSearch = '';
        $this->itemSearch = '';
    }

    public function clearMember(): void
    {
        $this->selectedMemberId = null;
        $this->selectedItemId = null;
        $this->loanType = '';
        $this->memberSearch = '';
        $this->itemSearch = '';
    }

    public function selectItem(int $itemId): void
    {
        abort_unless($this->selectedMemberId, 403);

        $item = $this->eligibleItems()
            ->with('material.materialType.loanTerm')
            ->whereKey($itemId)
            ->firstOrFail();

        $this->selectedItemId = $item->id;
        $this->itemSearch = '';
        $this->loanType = $this->availableLoanTypes($item)[0]['value'] ?? '';
    }

    public function clearItem(): void
    {
        $this->selectedItemId = null;
        $this->loanType = '';
        $this->itemSearch = '';
    }

    public function render()
    {
        $selectedMember = $this->selectedMemberId
            ? $this->eligibleMembers()->with('person')->find($this->selectedMemberId)
            : null;

        $selectedItem = $this->selectedMemberId && $this->selectedItemId
            ? $this->eligibleItems()->with([
                'material.materialType.loanTerm',
                'material.authors',
                'status',
                'container.location',
            ])->find($this->selectedItemId)
            : null;

        $members = mb_strlen(trim($this->memberSearch)) >= 2
            ? $this->eligibleMembers()
                ->whereHas('person', fn ($query) => $query
                    ->where('name', 'like', '%' . trim($this->memberSearch) . '%')
                    ->orWhere('last_name', 'like', '%' . trim($this->memberSearch) . '%')
                    ->orWhere('email', 'like', '%' . trim($this->memberSearch) . '%')
                    ->orWhere('dni', 'like', '%' . trim($this->memberSearch) . '%'))
                ->with('person')
                ->orderBy('id')
                ->limit(10)
                ->get()
            : collect();

        $items = $this->selectedMemberId && mb_strlen(trim($this->itemSearch)) >= 2
            ? $this->eligibleItems()
                ->with(['material.materialType', 'status', 'container.location'])
                ->where(function ($query): void {
                    $term = '%' . trim($this->itemSearch) . '%';
                    $query->where('barcode', 'like', $term)
                        ->orWhereHas('material', function ($materialQuery) use ($term): void {
                            $materialQuery->where('title', 'like', $term)
                                ->orWhere('identifier_value', 'like', $term)
                                ->orWhereHas('authors', fn ($authorQuery) =>
                                    $authorQuery->where('name', 'like', $term));
                        });
                })
                ->orderBy('barcode')
                ->limit(10)
                ->get()
            : collect();

        $loanTypes = $selectedItem ? $this->availableLoanTypes($selectedItem) : [];

        return view('livewire.circulation.staff-circulation-form', compact(
            'selectedMember',
            'selectedItem',
            'members',
            'items',
            'loanTypes',
        ));
    }

    private function eligibleMembers()
    {
        return User::query()
            ->where('is_active', true)
            ->whereHas('role', fn ($query) => $query
                ->where('name', 'member')
                ->where('is_active', true));
    }

    private function eligibleItems()
    {
        return Item::query()
            ->where('is_active', true)
            ->whereHas('material', fn ($query) => $query->where('is_active', true))
            ->where(function ($query): void {
                $query->whereHas('status', fn ($status) => $status->where('name', 'Disponible'))
                    ->orWhereHas('reservations', fn ($reservation) => $reservation
                        ->where('user_id', $this->selectedMemberId)
                        ->whereHas('status', fn ($status) => $status
                            ->where('name', Reservation::STATUS_READY)));
            });
    }

    private function availableLoanTypes(Item $item): array
    {
        $loanTerm = $item->material?->materialType?->loanTerm;
        $types = [];

        if ($loanTerm?->is_home_loanable) {
            $types[] = [
                'value' => Circulation::TYPE_HOME,
                'label' => 'Préstamo a domicilio',
                'duration' => $loanTerm->home_loan_days . ' días',
            ];
        }

        if ($loanTerm?->allow_in_library_use) {
            $types[] = [
                'value' => Circulation::TYPE_IN_LIBRARY,
                'label' => 'Consulta en sala',
                'duration' => $loanTerm->in_library_loan_hours . ' horas',
            ];
        }

        return $types;
    }
}
