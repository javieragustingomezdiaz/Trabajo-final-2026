<?php

namespace App\Livewire\Circulation;

use App\Models\Material;
use App\Models\Reservation;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Locked;
use Livewire\Component;

class StaffReservationForm extends Component
{
    public string $memberSearch = '';
    public string $materialSearch = '';

    #[Locked]
    public ?int $selectedMemberId = null;

    #[Locked]
    public ?int $selectedMaterialId = null;

    public function boot(): void
    {
        Gate::authorize('createForMember', Reservation::class);
    }

    public function mount(): void
    {
        $this->selectedMemberId = old('member_id') ? (int) old('member_id') : null;
        $this->selectedMaterialId = old('material_id') ? (int) old('material_id') : null;
    }

    public function selectMember(int $memberId): void
    {
        $member = $this->eligibleMembers()->whereKey($memberId)->firstOrFail();

        $this->selectedMemberId = $member->id;
        $this->selectedMaterialId = null;
        $this->memberSearch = '';
        $this->materialSearch = '';
    }

    public function clearMember(): void
    {
        $this->selectedMemberId = null;
        $this->selectedMaterialId = null;
        $this->memberSearch = '';
        $this->materialSearch = '';
    }

    public function selectMaterial(int $materialId): void
    {
        abort_unless($this->selectedMemberId, 403);

        $material = Material::query()
            ->active()
            ->whereKey($materialId)
            ->firstOrFail();

        $this->selectedMaterialId = $material->id;
        $this->materialSearch = '';
    }

    public function clearMaterial(): void
    {
        $this->selectedMaterialId = null;
        $this->materialSearch = '';
    }

    public function render()
    {
        $selectedMember = $this->selectedMemberId
            ? $this->eligibleMembers()->with('person')->find($this->selectedMemberId)
            : null;

        $selectedMaterial = $this->selectedMaterialId
            ? Material::query()->active()->with('materialType')->find($this->selectedMaterialId)
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

        $materials = $this->selectedMemberId && mb_strlen(trim($this->materialSearch)) >= 2
            ? Material::query()
                ->active()
                ->with('materialType')
                ->where(function ($query): void {
                    $term = '%' . trim($this->materialSearch) . '%';
                    $query->where('title', 'like', $term)
                        ->orWhere('identifier_value', 'like', $term)
                        ->orWhereHas('authors', fn ($authorQuery) => $authorQuery->where('name', 'like', $term));
                })
                ->orderBy('title')
                ->limit(10)
                ->get()
            : collect();

        return view('livewire.circulation.staff-reservation-form', compact(
            'selectedMember',
            'selectedMaterial',
            'members',
            'materials',
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
}
