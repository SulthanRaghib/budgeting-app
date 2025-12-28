@php
    $data = $this->getData();
    $hasData = count($data['labels']) > 0;
@endphp

<div class="flex items-center justify-center" style="min-height: 180px;">
    @if($hasData)
        <canvas id="expenseCategoryChart-{{ spl_object_id($this) }}" style="max-height:220px; width:100%; height:220px;"></canvas>
    @else
        <div class="text-sm text-gray-500">No expense data for this month</div>
    @endif
</div>

@if($hasData)
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (function() {
        const ctx = document.getElementById('expenseCategoryChart-{{ spl_object_id($this) }}').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: {!! json_encode($data['labels']) !!},
                datasets: [{
                    data: {!! json_encode($data['data']) !!},
                    backgroundColor: {!! json_encode($data['colors']) !!},
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    })();
</script>
@endif
