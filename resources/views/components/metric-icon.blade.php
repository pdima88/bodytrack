@props(['name'])

@php
    $paths = [
        'weight_kg' => '<rect x="4" y="4" width="16" height="16" rx="3.5"/><path d="M8.3 10.2a4.5 4.5 0 0 1 7.4 0"/><path d="M12 10.5 13.6 8"/>',
        'fat_percent' => '<circle cx="8" cy="9" r="2.6"/><circle cx="16" cy="9" r="2.6"/><circle cx="12" cy="15.5" r="2.6"/>',
        'water_percent' => '<path d="M12 3.8s5.8 6.3 5.8 10.2a5.8 5.8 0 0 1-11.6 0C6.2 10.1 12 3.8 12 3.8z"/>',
        'muscle_percent' => '<path d="M6.5 6.5v11"/><path d="M17.5 6.5v11"/><path d="M3.5 9.5v5"/><path d="M20.5 9.5v5"/><path d="M6.5 12h11"/>',
        'bone_percent' => '<path d="M17.9 9.6a2.2 2.2 0 1 0-3.5-2.5l-5.8 5.8a2.2 2.2 0 1 0-2.5 3.5 2.2 2.2 0 1 0 3.5 2.5l5.8-5.8a2.2 2.2 0 1 0 2.5-3.5z"/>',
        'visceral_fat' => '<circle cx="12" cy="12" r="7.6"/><circle cx="12" cy="12" r="3.4"/>',
        'bmi' => '<circle cx="12" cy="6" r="2.4"/><path d="M8 12.5h8"/><path d="M12 8.4V20"/><path d="M9 20h6"/>',
        'bmr_kcal' => '<path d="M12 20a5.6 5.6 0 0 0 5.6-5.6C17.6 10.6 12 4 12 4S6.4 10.6 6.4 14.4A5.6 5.6 0 0 0 12 20z"/><path d="M12 20a2.4 2.4 0 0 0 2.4-2.4C14.4 15.6 12 13.6 12 13.6s-2.4 2-2.4 4A2.4 2.4 0 0 0 12 20z"/>',
    ];
@endphp

<svg {{ $attributes->merge(['class' => 'w-4 h-4']) }} viewBox="0 0 24 24" fill="none" stroke="currentColor"
     stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">{!! $paths[$name] ?? '' !!}</svg>
