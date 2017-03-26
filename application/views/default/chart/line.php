<?=$this->template->get_view('elements/viewlist/header_ym')?>
<?=$this->template->get_view('elements/viewlist/button_group')?>

<div class="container">

    <!-- Styles -->
    <style>
        #chartdiv {
            width: 100%;
            height: 300px;
            margin-bottom: 20px;
        }													
    </style>
    
    <!-- Resources -->
    <script type="text/javascript" src="<?=base_url()?>asset/upload/amcharts/amcharts.js"></script>
    <script type="text/javascript" src="<?=base_url()?>asset/upload/amcharts/themes/black.js"></script>
    <script type="text/javascript" src="<?=base_url()?>asset/upload/amcharts/serial.js"></script>
    
    <!-- Chart code -->
    <script type="text/javascript">
        (function(){
            var chartData = <?=json_encode($list)?>;
            var dateFormat = genDateFormat(chartData);
            
            var chart = AmCharts.makeChart("chartdiv", {
                "fontFamily": "Arial",
                "type": "serial",
                "theme": "black",
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
                    "lineColor": "white",
                    "dashLength": 0,
                    "lineThickness": 2,
                    "bullet": false,
                    "bulletBorderThickness": 1,
                    "hideBulletsCount": 30,
                    "title": "Tổng",
                    "valueField": "tong",
                    "fillAlphas": 0
                }, {
                    "valueAxis": "v1",
                    "lineColor": "#00FFFF",
                    "dashLength": 2,
                    "lineThickness": 1,
                    "bullet": false,
                    "bulletBorderThickness": 1,
                    "hideBulletsCount": 30,
                    "title": "Thu",
                    "valueField": "thu",
                    "fillAlphas": 0
                }, {
                    "valueAxis": "v1",
                    "lineColor": "yellow",
                    "dashLength": 2,
                    "lineThickness": 1,
                    "bullet": false,
                    "bulletBorderThickness": 1,
                    "hideBulletsCount": 30,
                    "title": "Chi",
                    "valueField": "chi",
                    "fillAlphas": 0
                }],
                "chartScrollbar": {},
                "chartCursor": {
                    "cursorPosition": "mouse",
                    "bulletsEnabled": true,
                    "categoryBalloonDateFormat": dateFormat.categoryBalloonDateFormat,
                },
                "categoryField": 'date',
                "dataDateFormat": dateFormat.dataDateFormat,
                "categoryAxis": {
                    "parseDates": true,
                    "minPeriod": dateFormat.minPeriod,
                    "axisColor": "#333",
                    "axisAlpha": 1,
                    "gridThickness": 0,
                    "minorGridEnabled": true
                },
                "export": {
                    "enabled": false,
                }
            });
            
            chart.addListener("dataUpdated", zoomChart);
            chart.addListener("init", zoomChart);
            zoomChart();
            
            
            // Generate setting for dataDateFormat and categoryAxis.minPeriod
            function genDateFormat(chartData) {
                if (chartData.length == 0 || typeof(chartData[0]['date']) === 'undefined') {
                    return false;
                }
                
                var testDate = chartData[0]['date'];
                switch (true) {
                    case new RegExp(/^\d{4}$/).test(testDate):
                        return {
                            dataDateFormat: 'YYYY', 
                            categoryBalloonDateFormat: 'YYYY',
                            minPeriod: 'YYYY', 
                        };
                    case new RegExp(/^\d{4}\-\d{2}$/).test(testDate):
                        return {
                            dataDateFormat: 'YYYY-MM', 
                            categoryBalloonDateFormat: 'YYYY-MM',
                            minPeriod: 'MM', 
                        };
                    case new RegExp(/^\d{4}\-\d{2}\-\d{2}$/).test(testDate):
                        return {
                            dataDateFormat: 'YYYY-MM-DD', 
                            categoryBalloonDateFormat: 'YYYY-MM-DD',
                            minPeriod: 'DD',
                        };
                }
            }
            
            function zoomChart(){
                chart.zoomToIndexes(chart.dataProvider.length - chart.dataProvider.length, chart.dataProvider.length - 1);
            }
        })();
    
    </script>
    
    <!-- HTML -->
    <div id="chartdiv"></div>
    
</div>