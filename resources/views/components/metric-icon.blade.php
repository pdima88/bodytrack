@props(['name'])

@php
    // Pictograms mirror the icons printed on the Geepas GBS46505UK box:
    // fat = fat cells, hydration = waves, muscle = flexed biceps, bone,
    // weight = gym weight, kcal/BMI = lettering as on the LCD.
    $paths = [
        'weight_kg' => '<path d="M8.6 8.5 5.8 19h12.4L15.4 8.5z"/><rect x="9.3" y="4.5" width="5.4" height="4" rx="1.3"/>',
        'fat_percent' => '<ellipse cx="7.2" cy="9" rx="2.5" ry="1.9"/><ellipse cx="13" cy="8.2" rx="2.5" ry="1.9"/><ellipse cx="17.8" cy="10.6" rx="2" ry="1.6"/><ellipse cx="9.4" cy="14.8" rx="2.5" ry="1.9"/><ellipse cx="15.4" cy="15.4" rx="2.4" ry="1.8"/>',
        'water_percent' => '<path d="M4 8c1.3-1.5 2.7-1.5 4 0s2.7 1.5 4 0 2.7-1.5 4 0 2.7 1.5 4 0"/><path d="M4 12.2c1.3-1.5 2.7-1.5 4 0s2.7 1.5 4 0 2.7-1.5 4 0 2.7 1.5 4 0"/><path d="M4 16.4c1.3-1.5 2.7-1.5 4 0s2.7 1.5 4 0 2.7-1.5 4 0 2.7 1.5 4 0"/>',
        'muscle_percent' => '<path d="M6 4.8c-.9 3.8-1 7.4-.3 10 .8 2.7 3.1 4.2 6.3 4.2 4 0 6.8-2.1 6.8-5 0-2.5-1.9-4.2-4.5-4.2-1.4 0-2.6.5-3.4 1.4-.9-1.5-1.5-3.7-1.7-6.4z"/><path d="M6 4.8h3.2"/>',
        'bone_percent' => '<path d="M17.9 9.6a2.2 2.2 0 1 0-3.5-2.5l-5.8 5.8a2.2 2.2 0 1 0-2.5 3.5 2.2 2.2 0 1 0 3.5 2.5l5.8-5.8a2.2 2.2 0 1 0 2.5-3.5z"/>',
        'visceral_fat' => '<circle cx="12" cy="12" r="7.6"/><circle cx="12" cy="12" r="3.4"/>',
        'bmi' => '<text x="12" y="16" text-anchor="middle" font-size="11" font-weight="700" fill="currentColor" stroke="none" font-family="inherit">BMI</text>',
        'bmr_kcal' => '<text x="12" y="15.5" text-anchor="middle" font-size="8.5" font-weight="700" fill="currentColor" stroke="none" font-family="inherit" letter-spacing="-0.4">KCAL</text>',
    ];
@endphp

<svg {{ $attributes->merge(['class' => 'w-4 h-4']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor"
     stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $paths[$name] ?? '' !!}</svg>
