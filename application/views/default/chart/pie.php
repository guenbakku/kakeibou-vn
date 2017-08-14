<?=$this->template->get_view('elements/page-nav.php')?>
<?=$this->template->get_view('elements/timeline/header', ['date_element' => 'header_ymd'])?>

<div class="container">    
    <div class="well well-sm">
        <?=form_open($url['subForm'], array('method'=>'get', 'id' => 'subForm', 'class' => 'form-horizon'))?>
            <div class="row">
                <div class="col-xs-6">
                    <label>Loại</label>
                    <?=form_dropdown(
                        'inout_type', 
                        $select['inout_types'], 
                        $inout_type_id, 
                        array(
                            'class' => 'form-control submit-on-change',
                        )
                    )?>
                </div>
            </div>
        </form>
    </div>
    
    <?php if (count(array_filter($list, function($item){return $item['total'] > 0;}))): ?>
        <!-- Styles -->
        <style>
            #chart-box {
                width: 100%;
                height: 400px;
            }
            #legend-box {
                width: 100%;
                margin-bottom: 20px;
            }
        </style>
        
        <!-- Resources -->
        <script type="text/javascript" src="<?=base_url()?>asset/upload/amcharts/amcharts.js"></script>
        <script type="text/javascript" src="<?=base_url()?>asset/upload/amcharts/themes/black.js"></script>
        <script type="text/javascript" src="<?=base_url()?>asset/upload/amcharts/pie.js"></script>
        
        <!-- Chart code -->
        <script type="text/javascript">
            function sum(data) {
                var sum = 0;
                $.each(data, function(i, item){
                    sum += parseFloat(item.total) || 0;
                });
                return sum;
            }
            
            (function(){
                var chartData = <?=json_encode($list)?>;
                var chartConfig = {
                    "fontFamily": "Arial",
                    "type": "pie",
                    "theme": "black",
                    "startDuration": 0,
                    "labelsEnabled": false,
                    "marginLeft": 10,
                    "marginRight": 10,
                    "autoMargins": false,
                    "pullOutRadius": 0,
                    "legend": {
                        "divId": "legend-box",
                        "position":"bottom",
                        "autoMargins":false,
                    },
                    "dataProvider": chartData,
                    "valueField": "total",
                    "titleField": "category_name",
                    "innerRadius": "35%",
                    "balloon":{
                        "fixedPosition": false,
                    },
                    "export": {
                        "enabled": false
                    },
                    "allLabels": [{
                        "text": "Tổng",
                        "align": "center",
                        "bold": true,
                        "y": 180
                    }, {
                        "text": sum(chartData).toLocaleString(),
                        "align": "center",
                        "bold": false,
                        "y": 200
                    }],
                };
                var chart = AmCharts.makeChart( "chart-box", chartConfig);
            })();
        </script>
        
        <div id="chart-box"></div>
        <div id="legend-box"></div>
    <?php else: ?>
        <div class="panel panel-default">
            <div class="panel-body">
                <?=Consts::ERR_NOT_FOUND?>
            </div>
        </div>
    <?php endif ?>
</div>