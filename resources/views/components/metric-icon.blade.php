@props(['name'])

@php
    // Pictograms traced from the Geepas GBS46505UK box and LCD (see /icons
    // photos): filled fat cells, hydration waves, double-biceps figure,
    // filled bone, weight with ring knob, framed oval for visceral fat,
    // BMI/KCAL lettering. KCAL uses a wider canvas so the text stays legible.
    $wide = $name === 'bmr_kcal';

    $icons = [
        'weight_kg' => '<path fill="currentColor" d="M8.3 8.4 5.6 19h12.8L15.7 8.4z"/><circle cx="12" cy="5.9" r="2.1" fill="none" stroke="currentColor" stroke-width="1.7"/>',
        'fat_percent' => '<g fill="currentColor"><ellipse cx="6.8" cy="9.2" rx="2.5" ry="1.7"/><ellipse cx="12.2" cy="8.6" rx="2.5" ry="1.7"/><ellipse cx="17.5" cy="9.7" rx="2.1" ry="1.5"/><ellipse cx="9.3" cy="14.7" rx="2.5" ry="1.7"/><ellipse cx="15" cy="14.9" rx="2.5" ry="1.7"/></g>',
        'water_percent' => '<g stroke="currentColor" stroke-width="1.7" stroke-linecap="round" fill="none"><path d="M4 8c1.3-1.5 2.7-1.5 4 0s2.7 1.5 4 0 2.7-1.5 4 0 2.7 1.5 4 0"/><path d="M4 12.2c1.3-1.5 2.7-1.5 4 0s2.7 1.5 4 0 2.7-1.5 4 0 2.7 1.5 4 0"/><path d="M4 16.4c1.3-1.5 2.7-1.5 4 0s2.7 1.5 4 0 2.7-1.5 4 0 2.7 1.5 4 0"/></g>',
        'muscle_percent' => '<circle cx="12" cy="5.6" r="1.9" fill="currentColor"/><path fill="currentColor" d="M8.7 19.5v-5c0-2.1 1.3-3.4 3.3-3.4s3.3 1.3 3.3 3.4v5z"/><g stroke="currentColor" stroke-width="2.2" stroke-linecap="round" fill="none"><path d="M9.2 11.6C7.4 11.2 6.2 10.3 5.8 8.8L5.4 6.2"/><path d="M14.8 11.6c1.8-.4 3-1.3 3.4-2.8l.4-2.6"/></g>',
        'bone_percent' => '<g fill="currentColor"><circle cx="6.8" cy="10.2" r="2.2"/><circle cx="6.8" cy="13.8" r="2.2"/><circle cx="17.2" cy="10.2" r="2.2"/><circle cx="17.2" cy="13.8" r="2.2"/><rect x="6.8" y="10.3" width="10.4" height="3.4"/></g>',
        'visceral_fat' => '<rect x="3" y="4.5" width="18" height="15" rx="3.5" stroke="currentColor" stroke-width="1.7" fill="none"/><ellipse cx="12" cy="12" rx="5" ry="3.1" fill="currentColor"/>',
        'bmi' => '<text x="12" y="16" text-anchor="middle" font-size="11" font-weight="700" fill="currentColor" font-family="inherit">BMI</text>',
        'bmr_kcal' => '<text x="20" y="16.2" text-anchor="middle" font-size="11.5" font-weight="700" fill="currentColor" font-family="inherit" letter-spacing="0.5">KCAL</text>',
    ];
@endphp

<svg {{ $attributes->merge(['class' => $wide ? 'w-8 h-5' : 'w-5 h-5']) }} viewBox="{{ $wide ? '0 0 40 24' : '0 0 24 24' }}"
     fill="none" aria-hidden="true">{!! $icons[$name] ?? '' !!}</svg>
