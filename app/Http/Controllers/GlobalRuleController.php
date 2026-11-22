<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateGlobalRuleRequest;
use App\Models\GlobalRule;
use App\Services\GlobalRuleService;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class GlobalRuleController extends Controller
{
    use AuthorizesRequests;

    public function edit(GlobalRuleService $service): View
    {
        $this->authorize('manage', GlobalRule::class);

        $globalRule = $service->current();

        return view('global-rules.edit', compact('globalRule'));
    }

    public function update(UpdateGlobalRuleRequest $request, GlobalRuleService $service): RedirectResponse
    {
        $service->update($request->validated());

        return redirect()
            ->route('global-rules.edit')
            ->with('success', 'Los parámetros globales se actualizaron correctamente.');
    }
}
