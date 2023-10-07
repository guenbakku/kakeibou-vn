<?=$this->template->get_view('elements/page-nav.php')?>
<?=$this->template->get_view('elements/timeline/header', ['date_element' => 'header_ym'])?>

<div class="container">
    <div class="list-group">
        <?php foreach($list as $k => $v) : ?>
        <a class="list-group-item" page-scroll="<?=$v['date']?>" href="<?=sprintf($url['detailTemplate'], $v['date'])?>">
            <div class="row">
                <div class="pull-right" style="padding-right:15px; position:absolute; right:0px">
                    <span class="glyphicon glyphicon-menu-right"></span>
                </div>
                <div class="col-xs-11">
                    <div class="row">
                        <div class="col-xs-12"><span class="label label-default"><?=$v['date']?></span></div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6 text-right">Danh nghĩa</div>
                        <div class="col-xs-6 text-right">Tổng hợp</div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6 text-right text-income"><?=currency($v['thu_temp'])?></div>
                        <div class="col-xs-6 text-right text-income"><?=currency($v['thu'])?></div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6 text-right text-outgo"><?=currency($v['chi_temp'])?></div>
                        <div class="col-xs-6 text-right text-outgo"><?=currency($v['chi'])?></div>
                    </div>
                    <hr style="margin:5px 0">
                    <div class="row">
                        <div class="col-xs-6 text-right"><strong><?=currency($v['thu_temp'] + $v['chi_temp'])?></strong></div>
                        <div class="col-xs-6 text-right"><strong><?=currency($v['tong'])?></strong></div>
                    </div>
                </div>
            </div>
        </a>
        <?php endforeach ?>
    </div>
</div>

<script type="text/javascript">
    pageScroll('<?=$pageScrollTarget?>', -50);
</script>
