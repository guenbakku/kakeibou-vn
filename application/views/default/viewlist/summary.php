<script type="text/javascript">
    navigation('#subNav');
    
    $(function(){
        $('.submit-on-change').change(function(){
            $(this).parents('form').submit();
        });
    });
</script>

<div class="container">
    <div id="subNav" class="btn-group btn-group-justified">
        <a class="btn btn-default" href="<?=base_url().$this->router->fetch_class().'/'.$this->router->fetch_method()?>/day">Ngày</a>
        <a class="btn btn-default" href="<?=base_url().$this->router->fetch_class().'/'.$this->router->fetch_method()?>/month">Tháng</a>
        <a class="btn btn-default" href="<?=base_url().$this->router->fetch_class().'/'.$this->router->fetch_method()?>/year">Năm</a>
    </div>
</div>
<br>

<?php if (in_array($mode, array('day', 'week', 'month'))): ?>
<div class="container">
    <div class="well">
        <?=form_open($form_url, array('method'=>'get', 'id' => 'addCashFlow', 'class' => 'form-horizon'))?>
            <div class="row">
                <div class="col-xs-6">
                    <label>Năm</label>
                    <?=form_dropdown(
                        $field_name = 'year', 
                        $select['year'], 
                        $year, 
                        array(
                            'class' => 'form-control submit-on-change',
                        )
                    )?>
                </div>
                <?php if ($mode == 'day'): ?>
                <div class="col-xs-6">
                    <label>Tháng</label>
                    <?=form_dropdown(
                        $field_name = 'month', 
                        $select['month'], 
                        $month, 
                        array(
                            'class' => 'form-control submit-on-change',
                        )
                    )?>
                </div>
                <?php endif ?>
            </div>
        </form>
    </div>
</div>
<?php endif ?>

<div class="container">
    <div class="panel panel-default">
        <div class="list-group">
            <?php foreach($list as $k => $v) : ?>
            <a class="list-group-item" href="<?=base_url().'viewlist/inouts_of_day/'.$k?>">
                <div class="row">
                    <div class="pull-right" style="padding-right:15px; position:absolute; right:0px">
                        <span class="glyphicon glyphicon-menu-right"></span>
                    </div>
                    <div class="col-xs-11">
                        <div class="row">
                            <div class="col-xs-6"><span class="label label-default"><?=$k?></span></div>
                            <div class="col-xs-6 text-right text-blue"><?=currency($v['thu'])?></div>
                        </div>
                        <div class="row">
                            <div class="col-xs-6"><?=currency($v['tong'])?></div>
                            <div class="col-xs-6 text-right text-red"><?=currency($v['chi'])?></div>
                        </div>
                    </div>
                </div>
            </a>
            <?php endforeach ?>
        </div>
    </div>
</div>