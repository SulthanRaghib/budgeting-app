@php
    $cat = $record->category ?? null;
    $color = $cat?->color ?? '#6B7280';
    $name = $cat?->name ?? ($getState() ?? '—');

    // Ensure color is a valid CSS color; fallback handled above.

@endphp
<span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium"
    style="background-color: {{ $color }}; color: #ffffff;">
    {{ $name }}
</span>
