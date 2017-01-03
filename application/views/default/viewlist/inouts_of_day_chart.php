<div class="container">    
    <!-- Styles -->
    <style>
        #chartdiv {
            width: 100%;
            height: 500px;
            padding-bottom: 50px;
        }													
    </style>
    
    <!-- Resources -->
    <script type="text/javascript" src="<?=base_url()?>asset/upload/amcharts/amcharts.js"></script>
    <script type="text/javascript" src="<?=base_url()?>asset/upload/amcharts/pie.js"></script>
    
    <!-- Chart code -->
    <script type="text/javascript">
        (function(){
            var chart = AmCharts.makeChart( "chartdiv", {
                "fontFamily": "Arial",
                "type": "pie",
                "startDuration": 0,
                "labelsEnabled": false,
                "marginLeft": 10,
                "marginRight": 10,
                "pullOutRadius": 0,
                "legend":{
                    "position":"bottom",
                    "autoMargins":false,
                },
                "dataProvider": [ {
                    "country": "Lithuania",
                    "litres": 100
                }, {
                    "country": "Czech Republic",
                    "litres": 200
                }, {
                    "country": "Ireland",
                    "litres": 100
                }],
                "valueField": "litres",
                "titleField": "country",
                "balloon":{
                    "fixedPosition": false,
                },
                "export": {
                    "enabled": false
                }
            });
        })();
    </script>
    
    <div class="well well-sm">
        <?=form_open($url['form'], array('method'=>'get', 'id' => 'form', 'class' => 'form-horizon'))?>
            <div class="row">
                <div class="col-xs-6">
                    <label>Tài khoản</label>
                    <?=form_dropdown(
                        'account', 
                        $select['accounts'] + array('0' => 'Thực thu chi'), 
                        $account, 
                        array(
                            'class' => 'form-control submit-on-change',
                        )
                    )?>
                </div>
                <div class="col-xs-6">
                    <label>Loại</label>
                    <?=form_dropdown(
                        'inout_type', 
                        $select['inout_types'], 
                        $inout_type, 
                        array(
                            'class' => 'form-control submit-on-change',
                        )
                    )?>
                </div>
            </div>
        </form>
    </div>
    
    <div id="chartdiv"></div>
</div>