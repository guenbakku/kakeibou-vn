<script type="text/javascript">
    navigation('#subNav');
    pageScroll('<?=$page_scroll_target?>', -50);

    $(function(){
        $('.submit-on-change').change(function(){
            $(this).parents('form').submit();
        });
    });
</script>

<div class="container">
    <div id="subNav" class="btn-group btn-group-justified">
        <a class="btn btn-default" href="<?=$url['subNav'][0]?>">Ngày</a>
        <a class="btn btn-default" href="<?=$url['subNav'][1]?>">Tháng</a>
        <a class="btn btn-default" href="<?=$url['subNav'][2]?>">Năm</a>
    </div>
</div>
<br>

<?php if (in_array($mode, array('day', 'week', 'month'))): ?>
<div class="container">
    <div class="well well-sm">
        <?=form_open($url['form'], array('method'=>'get', 'id' => 'addCashFlow', 'class' => 'form-horizon'))?>
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
            <a class="list-group-item" page-scroll="<?=$k?>" href="<?=sprintf($url['inouts_of_day'], $k)?>">
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