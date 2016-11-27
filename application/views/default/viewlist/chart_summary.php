<?php
    $labels = array_map(function($day){
        return date('Y-m-d', strtotime('201601'. sprintf('%02d', $day)));
    }, range(1,31));
    
    $line[] = array_map(function($day){
        return rand(0, 50000);
    }, range(1,31));
    $line[] = array_map(function($day){
        return rand(-50000, 0);
    }, range(1,31));
    $line[] = array_map(function(){
        return array_sum(func_get_args());
    }, $line[0], $line[1]);
    
    $json['labels'] = json_encode($labels);
    $json['lines']   = array_map(function($item){
        return json_encode($item);
    }, $line);
    
?>

<div class="container">

    <!-- Styles -->
    <style>
    #chartdiv {
        width: 100%;
        height: 300px;
        padding-bottom: 50px;
    }													
    </style>
    
    <!-- Resources -->
    <script src="https://www.amcharts.com/lib/3/amcharts.js"></script>
    <script src="https://www.amcharts.com/lib/3/serial.js"></script>
    <script src="https://www.amcharts.com/lib/3/themes/light.js"></script>
    <script src="https://www.amcharts.com/lib/3/plugins/export/export.min.js"></script>
    <link rel="stylesheet" href="https://www.amcharts.com/lib/3/plugins/export/export.css" type="text/css" media="all" />
    
    <!-- Chart code -->
    <script>
    var chartData = generateChartData();
    
    var chart = AmCharts.makeChart("chartdiv", {
        "type": "serial",
        "theme": "light",
        "legend": {
            "useGraphSettings": true,
            "valueWidth": 60,
            "align": "left",
        },
        "dataProvider": chartData,
        "synchronizeGrid":true,
        "valueAxes": [{
            "id":"v1",
            "axisColor": "#333",
            "labelsEnabled": false,
            "inside": true,
            "axisThickness": 1,
            "gridThickness": 0,
            "axisAlpha": 1,
            "position": "left",
        }],
        "graphs": [{
            "valueAxis": "v1",
            "lineColor": "#FF6600",
            "bullet": true,
            "bulletBorderThickness": 1,
            "hideBulletsCount": 30,
            "title": "Thu",
            "valueField": "visits",
            "fillAlphas": 0
        }, {
            "valueAxis": "v1",
            "lineColor": "#FCD202",
            "bullet": false,
            "bulletBorderThickness": 1,
            "hideBulletsCount": 30,
            "title": "Chi",
            "valueField": "hits",
            "fillAlphas": 0
        }, {
            "valueAxis": "v1",
            "lineColor": "#B0DE09",
            "bullet": false,
            "bulletBorderThickness": 1,
            "hideBulletsCount": 30,
            "title": "Tổng",
            "valueField": "views",
            "fillAlphas": 0
        }],
        "chartScrollbar": {},
        "chartCursor": {
            "cursorPosition": "mouse",
            "bulletsEnabled": true,
        },
        "categoryField": 'date',
        "dataDateFormat": "YYYY-MM-DD",
        "categoryAxis": {
            "parseDates": true,
            "minPeriod": "DD",
            // "dateFormats": [{"period":"fff","format":"JJ:NN:SS"},{"period":"ss","format":"JJ:NN:SS"},{"period":"mm","format":"JJ:NN"},{"period":"hh","format":"JJ:NN"},{"period":"DD","format":"MM-DD"},{"period":"WW","format":"MMM DD"},{"period":"MM","format":"MMM"},{"period":"YYYY","format":"YYYY"}],
            "axisColor": "#333",
            "axisAlpha": 1,
            "gridThickness": 0,
            "minorGridEnabled": true
        },
        "export": {
            "enabled": false,
            "position": "bottom-right"
        }
    });
    
    chart.addListener("dataUpdated", zoomChart);
    chart.addListener("init", zoomChart);
    zoomChart();
    
    
    // generate some random data, quite different range
    function generateChartData() {
        var chartData = [];
        var firstDate = new Date();
        firstDate.setDate(firstDate.getDate() - 26);
    
        for (var i = 0; i < 31; i++) {
            // we create date objects here. In your data, you can have date strings
            // and then set format of your dates using chart.dataDateFormat property,
            // however when possible, use date objects, as this will speed up chart rendering.
            // var newDate = new Date(firstDate);
            // newDate.setDate(newDate.getDate() + i);
            newDate = i<9? '2016-11-0' : '2016-11-';
            newDate = newDate + (i+1);
    
            var visits = Math.round(Math.random()) * (1000000 - 0) + 0;
            var hits = Math.round(Math.random()) * (0 - 1000000) - 1000000;
            var views = visits + hits;
    
            chartData.push({
                date: newDate,
                visits: visits,
                hits: hits,
                views: views
            });
        }
        return chartData;
    }
    
    function zoomChart(){
        chart.zoomToIndexes(chart.dataProvider.length - chart.dataProvider.length, chart.dataProvider.length - 1);
    }
    
    </script>
    
    <!-- HTML -->
    <div id="chartdiv"></div>
    
</div>