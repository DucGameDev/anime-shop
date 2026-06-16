@props([
    'variant' => 'primary',
    'size'    => 'base',
    'type'    => 'button',
])

@php
$variantClasses = match ($variant) {
    'secondary' => 'bg-secondary text-white hover:bg-secondary/90 focus:ring-secondary',
    default     => 'bg-primary text-white hover:bg-primary-dark focus:ring-primary',
};

$sizeClasses = match ($size) {
    'sm'    => 'min-h-[36px] px-3 py-1.5 text-sm',
    default => 'min-h-[44px] px-5 py-2.5 text-sm md:text-base',
};
@endphp

<button
    type="{{ $type }}"
    {{ $attributes->merge(['class' => "inline-flex items-center justify-center font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed $variantClasses $sizeClasses"]) }}
>
    {{ $slot }}
</button>
