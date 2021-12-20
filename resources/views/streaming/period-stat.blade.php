<figure class="highcharts-figure">
    <div id="container12">
    </div>
</figure>
<script type="text/javascript">
    Highcharts.chart("container12", {
        chart: {
            type: "line"
        },
        title: {
            text: "Количество пользователей и групп среди авторов"
        },
        subtitle: {
            text: "за последние {{ $period_stat['last'] }}"
        },
        xAxis: {
            categories: [{!! $period_stat['date'] !!}]
        },
        yAxis: {
            title: {
                text: "Количество авторов"
            }
        },
        plotOptions: {
            line: {
                dataLabels: {
                    enabled: true
                },
                enableMouseTracking: false
            }
        },
        series: [{
            name: "Мужчины",
            data: [{{ $period_stat['male'] }}]
        }, {
            name: "Женщины",
            data: [{{ $period_stat['female'] }}]
        },{
            name: "Группы",
            data: [{{ $period_stat['group'] }}]
        }, {
            name: "Всего",
            data: [{{ $period_stat['alls'] }}]
        }]
    });
</script>
