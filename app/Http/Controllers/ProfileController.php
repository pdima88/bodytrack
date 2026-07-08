<?php

namespace App\Http\Controllers;

use App\Models\Profile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'profile' => $request->user()->profile,
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'sex' => ['required', Rule::in(Profile::SEXES)],
            'birth_date' => ['required', 'date', 'before:-10 years', 'after:-120 years'],
            'height_cm' => ['required', 'integer', 'min:100', 'max:250'],
            'activity_level' => ['required', Rule::in(Profile::ACTIVITY_LEVELS)],
            'target_weight_kg' => ['nullable', 'numeric', 'min:20', 'max:300'],
        ]);

        $isNew = $request->user()->profile === null;

        $request->user()->profile()->updateOrCreate([], $validated);

        return $isNew
            ? redirect()->route('dashboard')->with('status', __('app.profile.saved'))
            : redirect()->route('profile.edit')->with('status', __('app.profile.saved'));
    }
}
