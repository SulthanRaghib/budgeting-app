@php
    $target = (float) ($record->budget?->amount ?? 0);
    $spent = (float) ($record->transactions_sum_amount ?? 0);

    if ($target <= 0) {
        $percent = 0;
    } else {
        $percent = min(100, round(($spent / $target) * 100));
    }

    $barColor = $percent >= 100 ? 'bg-red-500' : ($percent >= 80 ? 'bg-yellow-400' : 'bg-green-500');
@endphp

<div class="fi-flex fi-items-center fi-gap-3">
    <div class="fi-flex-1">
        <div class="fi-h-3 fi-bg-gray-200 fi-rounded fi-overflow-hidden">
            <div class="{{ $barColor }} fi-h-full" style="width: {{ $percent }}%"></div>
        </div>
    </div>
    <div class="fi-text-xs fi-font-medium">
        {{ $percent }}%
    </div>
</div>
