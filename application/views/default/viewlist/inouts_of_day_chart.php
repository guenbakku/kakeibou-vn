<div class="container">    
    <div class="well well-sm">
        <?=form_open($url['form'], array('method'=>'get', 'id' => 'form', 'class' => 'form-horizon'))?>
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
            #chartdiv {
                width: 100%;
                height: <?=$inout_type_id == 1? 380 : 600?>px;
                margin-bottom: 20px;
            }													
        </style>
        
        <!-- Resources -->
        <script type="text/javascript" src="<?=base_url()?>asset/upload/amcharts/amcharts.js"></script>
        <script type="text/javascript" src="<?=base_url()?>asset/upload/amcharts/pie.js"></script>
        
        <!-- Chart code -->
        <script type="text/javascript">
            (function(){
                var chartData = <?=json_encode($list)?>;
                var chart = AmCharts.makeChart( "chartdiv", {
                    "fontFamily": "Arial",
                    "type": "pie",
                    "startDuration": 0,
                    "labelsEnabled": false,
                    "marginLeft": 10,
                    "marginRight": 10,
                    "autoMargins": false,
                    "pullOutRadius": 0,
                    "legend":{
                        "position":"bottom",
                        "autoMargins":false,
                    },
                    "dataProvider": chartData,
                    "valueField": "total",
                    "titleField": "category_name",
                    "balloon":{
                        "fixedPosition": false,
                    },
                    "export": {
                        "enabled": false
                    }
                });
            })();
        </script>
        
        <div id="chartdiv"></div>
    <?php else: ?>
        <div class="panel panel-default">
            <div class="panel-body">
                <?=Constants::ERR_NOT_FOUND?>
            </div>
        </div>
    <?php endif ?>
</div>