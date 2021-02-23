{$meta_title=$btr->stats_stats scope=global}

{*Название страницы*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="heading_page">{$btr->stats_stats|escape}
            <i class="fn_tooltips" title="{$btr->tooltip_stats_stats|escape}">
                {include file='svg_icon.tpl' svgId='icon_tooltips'}
            </i>
        </div>
    </div>
</div>

{*Контент статистики*}
<div class="row">
    <div class="col-lg-12 col-md-12">
        <div class="boxed fn_toggle_wrap">
            <div id='container'></div>
        </div>
    </div>
</div>
{* On document load *}
<script>
    var stats_orders = '{$btr->stats_orders|escape}';
    var stats_message = '{$btr->stats_message|escape}';
    var stats_orders_amount =  '{$btr->stats_orders_amount|escape}';
</script>
{literal}
    <script src="design/js/highcharts/highcharts.js" type="text/javascript"></script>
    <script src="design/js/highcharts/themes/avocado.js" type="text/javascript"></script>
    <script src="design/js/highcharts/modules/exporting.js" type="text/javascript"></script>

<script>
    var chart;
    $(function() {
        var options = {
            exporting: {
                chartOptions: { // specific options for the exported image
                    plotOptions: {
                        series: {
                            dataLabels: {
                                enabled: true
                            }
                        }
                    }
                },
                fallbackToExportServer: false
            },
            chart: {
                zoomType: 'x',
                renderTo: 'container',
                defaultSeriesType: 'area',
                type : "line"
            },
            title: {
                text: stats_orders
            },
            subtitle: {
                text: stats_message
            },
            xAxis: {
                type: 'datetime',
                minRange: 7 * 24 * 3600000,
                maxZoom: 7 * 24 * 3600000,
                gridLineWidth: 1,
                ordinal: true,
                showEmpty: false
            },
            yAxis: {
                title: {
                    text: '{/literal}{$currency->name}{literal}'
                }
            },


            plotOptions: {
                line: {
                    dataLabels: {
                        enabled: true
                    },
                    enableMouseTracking: true,
                    connectNulls: false
                },
                area: {
                    marker: {
                        enabled: false
                    },
                }
            },
            series: []

        };

        $.get('ajax/stat.php', function(data){
            var series = {
                data: []
            };


            var minDate = Date.UTC(data[0].year, data[0].month-1, data[0].day),
                maxDate = Date.UTC(data[data.length-1].year, data[data.length-1].month-1, data[data.length-1].day);

            var newDates = [], currentDate = minDate, d;

            while (currentDate <= maxDate) {
                d = new Date(currentDate);
                newDates.push((d.getMonth() + 1) + '/' + d.getDate() + '/' + d.getFullYear());
                currentDate += (24 * 60 * 60 * 1000); // add one day
            }
            series.name = stats_orders_amount + '{/literal}{$currency->sign}{literal}';

            // Iterate over the lines and add categories or series
            $.each(data, function(lineNo, line) {
                series.data.push([Date.UTC(line.year, line.month-1, line.day), parseInt(line.y)]);
            });
            //
            options.series.push(series);

            // Create the chart
            var chart = new Highcharts.Chart(options);
        });
    });
    // Apply the theme
    var highchartsOptions = Highcharts.setOptions(Highcharts.theme);

</script>
{/literal}
