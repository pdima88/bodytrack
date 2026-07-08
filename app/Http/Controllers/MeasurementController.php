<?php

namespace App\Http\Controllers;

use App\Models\Measurement;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MeasurementController extends Controller
{
    /**
     * Weight jumps beyond this many kg against the previous entry are treated
     * as a probable typo and require explicit confirmation.
     */
    private const ANOMALY_THRESHOLD_KG = 5.0;

    public function index(Request $request): View
    {
        return view('measurements.index', [
            'measurements' => $request->user()->measurements()
                ->orderByDesc('measured_at')
                ->paginate(20),
        ]);
    }

    public function create(Request $request): View
    {
        return view('measurements.create', [
            'measurement' => null,
            'last' => $request->user()->measurements()->orderByDesc('measured_at')->first(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validated($request);

        if ($redirect = $this->anomalyRedirect($request, $validated)) {
            return $redirect;
        }

        $request->user()->measurements()->create($validated);

        return redirect()->route('dashboard')->with('status', __('app.measurements.saved'));
    }

    public function edit(Request $request, Measurement $measurement): View
    {
        $this->ensureOwner($request, $measurement);

        return view('measurements.create', [
            'measurement' => $measurement,
            'last' => null,
        ]);
    }

    public function update(Request $request, Measurement $measurement): RedirectResponse
    {
        $this->ensureOwner($request, $measurement);

        $measurement->update($this->validated($request));

        return redirect()->route('measurements.index')->with('status', __('app.measurements.updated'));
    }

    public function destroy(Request $request, Measurement $measurement): RedirectResponse
    {
        $this->ensureOwner($request, $measurement);

        $measurement->delete();

        return redirect()->route('measurements.index')->with('status', __('app.measurements.deleted'));
    }

    public function export(Request $request): StreamedResponse
    {
        $measurements = $request->user()->measurements()->orderBy('measured_at')->get();

        $columns = [
            'measured_at' => __('app.measurements.measured_at'),
            'weight_kg' => __('app.measurements.weight'),
            'fat_percent' => __('app.measurements.fat_percent'),
            'water_percent' => __('app.measurements.water_percent'),
            'muscle_percent' => __('app.measurements.muscle_percent'),
            'bone_kg' => __('app.measurements.bone_kg'),
            'visceral_fat' => __('app.measurements.visceral_fat'),
            'bmi' => __('app.measurements.bmi'),
            'bmr_kcal' => __('app.measurements.bmr_kcal'),
        ];

        return response()->streamDownload(function () use ($measurements, $columns) {
            $out = fopen('php://output', 'w');

            // UTF-8 BOM so Excel detects the encoding
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, array_values($columns), ';');

            foreach ($measurements as $m) {
                $row = [$m->measured_at->format('d.m.Y H:i')];
                foreach (array_slice(array_keys($columns), 1) as $field) {
                    $row[] = $m->{$field} !== null ? str_replace('.', ',', (string) $m->{$field}) : '';
                }
                fputcsv($out, $row, ';');
            }

            fclose($out);
        }, 'bodytrack-' . now()->format('Y-m-d') . '.csv', [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'measured_at' => ['required', 'date', 'before_or_equal:now'],
            'weight_kg' => ['required', 'numeric', 'min:10', 'max:180'],
            'fat_percent' => ['nullable', 'numeric', 'min:3', 'max:75'],
            'water_percent' => ['nullable', 'numeric', 'min:20', 'max:85'],
            'muscle_percent' => ['nullable', 'numeric', 'min:10', 'max:75'],
            'bone_kg' => ['nullable', 'numeric', 'min:0.5', 'max:10'],
            'visceral_fat' => ['nullable', 'integer', 'min:1', 'max:59'],
            'bmi' => ['nullable', 'numeric', 'min:8', 'max:80'],
            'bmr_kcal' => ['nullable', 'integer', 'min:500', 'max:5000'],
        ]);
    }

    private function anomalyRedirect(Request $request, array $validated): ?RedirectResponse
    {
        if ($request->boolean('confirm_anomaly')) {
            return null;
        }

        $last = $request->user()->measurements()->orderByDesc('measured_at')->first();

        if ($last === null || abs($validated['weight_kg'] - $last->weight_kg) <= self::ANOMALY_THRESHOLD_KG) {
            return null;
        }

        return back()->withInput()->with('anomaly', __('app.measurements.anomaly_warning', [
            'previous' => number_format($last->weight_kg, 1, ',', ' '),
            'current' => number_format($validated['weight_kg'], 1, ',', ' '),
        ]));
    }

    private function ensureOwner(Request $request, Measurement $measurement): void
    {
        abort_if($measurement->user_id !== $request->user()->id, 404);
    }
}
