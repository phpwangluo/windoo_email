<canvas id="doughnut" width="200px" height="200"></canvas>
<script>
    $(function () {
        var config = {
            type: 'doughnut',
            data: {
                datasets: [{
                    data: [
                        {{ $email[0]->s }},
                        {{ $email[0]->r }},
                    ],
                    backgroundColor: [
                        'rgb(54, 162, 235)',
                        'rgb(255, 99, 132)',
                    ]
                }],
                labels: [
                    '发送次数',
                    '回复次数',
                ]
            },
            options: {
                maintainAspectRatio: false
            }
        };
        var ctx = document.getElementById('doughnut').getContext('2d');
        new Chart(ctx, config);
    });
</script>
