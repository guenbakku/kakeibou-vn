<?=$this->template->get_view('elements/viewlist/summary_header')?>

<div class="container">
    <div class="form-group">
        <a class="btn btn-default btn-sm" href="<?=$url['viewchart']?>">Biểu đồ</a>
    </div>
</div>

<div class="container">
    <div class="panel panel-default">
        <div class="list-group">
            <?php foreach($list as $k => $v) : ?>
            <a class="list-group-item" page-scroll="<?=$v['date']?>" href="<?=sprintf($url['detail_template'], $v['date'])?>">
                <div class="row">
                    <div class="pull-right" style="padding-right:15px; position:absolute; right:0px">
                        <span class="glyphicon glyphicon-menu-right"></span>
                    </div>
                    <div class="col-xs-11">
                        <div class="row">
                            <div class="col-xs-6"><span class="label label-default"><?=$v['date']?></span></div>
                            <div class="col-xs-6 text-right text-income"><?=currency($v['thu'])?></div>
                        </div>
                        <div class="row">
                            <div class="col-xs-6"><?=currency($v['tong'])?></div>
                            <div class="col-xs-6 text-right text-outgo"><?=currency($v['chi'])?></div>
                        </div>
                    </div>
                </div>
            </a>
            <?php endforeach ?>
        </div>
    </div>
</div>

<script type="text/javascript">
    pageScroll('<?=$pageScrollTarget?>', -50);
</script>