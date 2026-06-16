@props([
    'as'   => 'input',
    'type' => 'text',
])

@php
$baseClass = 'w-full rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm text-neutral-text placeholder-neutral-muted transition-colors focus:border-primary focus:outline-none focus:ring-2 focus:ring-primary/20';
@endphp

@if ($as === 'select')
    <select {{ $attributes->merge(['class' => $baseClass]) }}>
        {{ $slot }}
    </select>
@else
    <input
        type="{{ $type }}"
        {{ $attributes->merge(['class' => $baseClass]) }}
    />
@endif
