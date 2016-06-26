<script type="text/javascript">
    navigation('#subNav');
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
                <div class="col-xs-8">
                    <?=form_dropdown(
                        $field_name = 'year', 
                        $select['year'], 
                        $year, 
                        array(
                            'class' => 'form-control',
                        )
                    )?>
                </div>
                <div class="col-xs-4">
                    <button type="submit" class="btn btn-primary btn-sm">Xem</button>
                </div>
            </div>
            <?php if ($mode == 'day'): ?>
            <div class="row">
                <div class="col-xs-8">
                    <?=form_dropdown(
                        $field_name = 'month', 
                        $select['month'], 
                        $month, 
                        array(
                            'class' => 'form-control',
                        )
                    )?>
                </div>
            </div>
            <?php endif ?>
        </form>
    </div>
</div>
<?php endif ?>

<div class="container">
    <div class="panel panel-default">
        <div class="list-group">
            <?php foreach($list as $k => $v) : ?>
            <a class="list-group-item" href="<?=base_url().'summary/detail/'.$k?>">
                <div class="row">
                    <div class="pull-right" style="padding-right:15px; position:absolute; right:0px">
                        <span class="glyphicon glyphicon-menu-right"></span>
                    </div>
                    <div class="col-xs-11">
                        <div class="row">
                            <div class="col-xs-6"><span class="label label-default"><?=$k?></span></div>
                            <div class="col-xs-6 text-right text-blue">+<?=number_format($v['thu'])?></div>
                        </div>
                        <div class="row">
                            <div class="col-xs-6"><?=number_format($v['tong'])?></div>
                            <div class="col-xs-6 text-right text-red">-<?=number_format($v['chi'])?></div>
                        </div>
                    </div>
                </div>
            </a>
            <?php endforeach ?>
        </div>
    </div>
</div>