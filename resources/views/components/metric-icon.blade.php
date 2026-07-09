@props(['name'])

@php
    // Pictograms traced from the Geepas GBS46505UK box and LCD (see /icons
    // photos): filled fat cells, hydration waves, double-biceps figure,
    // filled bone, weight with ring knob, framed oval for visceral fat,
    // BMI/KCAL lettering. KCAL uses a wider canvas so the text stays legible.
    $wide = $name === 'bmr_kcal';

    $icons = [
        'weight_kg' => '<path fill="currentColor" d="M8.3 8.4 5.6 19h12.8L15.7 8.4z"/><circle cx="12" cy="5.9" r="2.1" fill="none" stroke="currentColor" stroke-width="1.7"/>',
        'fat_percent' => '<g fill="currentColor"><ellipse cx="6.4" cy="9" rx="2.5" ry="1.7"/><ellipse cx="12" cy="9" rx="2.5" ry="1.7"/><ellipse cx="17.6" cy="9" rx="2.5" ry="1.7"/><ellipse cx="9.2" cy="14.4" rx="2.5" ry="1.7"/><ellipse cx="14.8" cy="14.4" rx="2.5" ry="1.7"/></g>',
        'water_percent' => '<g stroke="currentColor" stroke-width="1.7" stroke-linecap="round" fill="none"><path d="M4 8c1.3-1.5 2.7-1.5 4 0s2.7 1.5 4 0 2.7-1.5 4 0 2.7 1.5 4 0"/><path d="M4 12.2c1.3-1.5 2.7-1.5 4 0s2.7 1.5 4 0 2.7-1.5 4 0 2.7 1.5 4 0"/><path d="M4 16.4c1.3-1.5 2.7-1.5 4 0s2.7 1.5 4 0 2.7-1.5 4 0 2.7 1.5 4 0"/></g>',
        'muscle_percent' => '<g fill="currentColor"><circle cx="12" cy="4.4" r="1.9"/><ellipse cx="12" cy="9.9" rx="4.9" ry="2.4"/><path d="M8.3 9h7.4l-1.3 10h-4.8z"/><ellipse cx="7.3" cy="10.5" rx="2.8" ry="1.8" transform="rotate(-22 7.3 10.5)"/><ellipse cx="16.7" cy="10.5" rx="2.8" ry="1.8" transform="rotate(22 16.7 10.5)"/><circle cx="5" cy="7.3" r="1.4"/><circle cx="19" cy="7.3" r="1.4"/></g><g stroke="currentColor" stroke-width="2.4" stroke-linecap="round"><path d="M5.5 11.3 5 7.7"/><path d="M18.5 11.3 19 7.7"/></g>',
        'bone_percent' => '<g fill="currentColor"><circle cx="6.8" cy="10.2" r="2.2"/><circle cx="6.8" cy="13.8" r="2.2"/><circle cx="17.2" cy="10.2" r="2.2"/><circle cx="17.2" cy="13.8" r="2.2"/><rect x="6.8" y="10.3" width="10.4" height="3.4"/></g>',
        'visceral_fat' => '<path fill="currentColor" fill-rule="evenodd" d="M3.5 12a8.5 5.6 0 1 0 17 0 8.5 5.6 0 1 0-17 0ZM6 10.3a1.5 1.5 0 1 0 3 0 1.5 1.5 0 1 0-3 0Zm4.5 0a1.5 1.5 0 1 0 3 0 1.5 1.5 0 1 0-3 0Zm4.5 0a1.5 1.5 0 1 0 3 0 1.5 1.5 0 1 0-3 0Zm-6.8 3.8a1.5 1.5 0 1 0 3 0 1.5 1.5 0 1 0-3 0Zm4.6 0a1.5 1.5 0 1 0 3 0 1.5 1.5 0 1 0-3 0Z"/>',
        'bmi' => '<text x="12" y="16" text-anchor="middle" font-size="11" font-weight="700" fill="currentColor" font-family="inherit">BMI</text>',
        'bmr_kcal' => '<text x="20" y="16.2" text-anchor="middle" font-size="11.5" font-weight="700" fill="currentColor" font-family="inherit" letter-spacing="0.5">KCAL</text>',
    ];
@endphp

<svg {{ $attributes->merge(['class' => $wide ? 'w-8 h-5' : 'w-5 h-5']) }} viewBox="{{ $wide ? '0 0 40 24' : '0 0 24 24' }}"
     fill="none" aria-hidden="true">{!! $icons[$name] ?? '' !!}</svg>
