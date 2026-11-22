<?php

namespace App\Http\Controllers;

use App\Http\Requests\SaveLoanTermRequest;
use App\Models\LoanTerm;
use App\Models\MaterialType;
use App\Services\LoanTermService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LoanTermController extends Controller
{
    use AuthorizesRequests;

    public function index(): View
    {
        $this->authorize('manage', LoanTerm::class);

        $materialTypes = MaterialType::query()
            ->with('loanTerm')
            ->orderBy('name')
            ->paginate(15);

        return view('loan-terms.index', compact('materialTypes'));
    }

    public function edit(MaterialType $materialType): View
    {
        $this->authorize('manage', LoanTerm::class);

        $loanTerm = $materialType->loanTerm;

        return view('loan-terms.edit', compact('materialType', 'loanTerm'));
    }

    public function update(
        SaveLoanTermRequest $request,
        MaterialType $materialType,
        LoanTermService $service
    ): RedirectResponse {
        $service->saveForMaterialType($materialType, $request->validated());

        return redirect()
            ->route('loan-terms.index')
            ->with('success', 'La regla de préstamo se guardó correctamente.');
    }
}
