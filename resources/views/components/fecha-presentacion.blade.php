@props(['fecha'])

@php
    $carbon = $fecha ? \Carbon\Carbon::parse($fecha) : null;
    $vencida  = $carbon && $carbon->isPast();
    $proxima  = $carbon && !$vencida && $carbon->diffInDays(now()) < 6;
@endphp

@if(!$carbon)
    <span class="text-neutral-400">—</span>
@elseif($vencida)
    <span class="flex items-center gap-1 text-red-600 font-semibold">
        <flux:icon.exclamation-triangle class="size-4 shrink-0" />
        {{ $carbon->format('d/m/Y') }}
    </span>
@elseif($proxima)
    <span class="flex items-center gap-1 text-amber-500 font-semibold">
        <flux:icon.clock class="size-4 shrink-0" />
        {{ $carbon->format('d/m/Y') }}
    </span>
@else
    <span class="text-neutral-700 dark:text-neutral-300">{{ $carbon->format('d/m/Y') }}</span>
@endif
