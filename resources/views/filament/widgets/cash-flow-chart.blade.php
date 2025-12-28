@php
    $data = $this->getData();
@endphp

<div style="min-height: 220px;">
    <canvas id="cashFlowChart-{{ spl_object_id($this) }}" style="width:100%; height:220px;"></canvas>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    (function() {
        const ctx = document.getElementById('cashFlowChart-{{ spl_object_id($this) }}').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: {!! json_encode($data['labels']) !!},
                datasets: [{
                        label: 'Income',
                        data: {!! json_encode($data['income']) !!},
                        borderColor: '#10b981', // success
                        backgroundColor: 'rgba(16,185,129,0.15)',
                        tension: 0.2,
                    },
                    {
                        label: 'Expense',
                        data: {!! json_encode($data['expense']) !!},
                        borderColor: '#ef4444', // danger
                        backgroundColor: 'rgba(239,68,68,0.12)',
                        tension: 0.2,
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    })();
</script>
