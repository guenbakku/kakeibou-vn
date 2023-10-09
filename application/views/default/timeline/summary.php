<?=$this->template->get_view('elements/page-nav.php')?>
<?=$this->template->get_view('elements/timeline/header', ['date_element' => 'header_ym'])?>

<div class="container">
    <div class="list-group">
        <?php foreach($list as $k => $v) : ?>
        <div page-scroll="<?=$v['date']?>" class="list-group-item">
            <div class="row">
                <div class="pull-right" style="padding-right:15px; position:absolute; right:0px">
                    <span class="glyphicon glyphicon-menu-right"></span>
                </div>
                <div class="col-xs-2" onclick="toggle_collapse_table_detail(this); return false">
                    <div class="label label-default">
                        <span class="glyphicon glyphicon-triangle-bottom"></span>
                    </div>
                </div>
                <a href="<?=sprintf($url['detailTemplate'], $v['date'])?>">
                    <div class="col-xs-3" style="padding-left:0">
                        <span class="label label-default"><?=$v['date']?></span>
                    </div>
                    <div class="col-xs-6 text-right <?=$v['tong']>=0 ? 'text-income' : 'text-outgo'?>" style="padding-left:0">
                        <?=currency($v['tong'])?>
                    </div>
                </a>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <div class="table-summarize-container collapse" aria-expanded="false">
                        <table class="table table-bordered table-summarize last-row-is-total">
                            <tr>
                                <th style="width:33%" class="text-center">Danh nghĩa</th>
                                <th style="width:33%" class="text-center">Thực tế</th>
                                <th style="width:33%" class="text-center">Tổng hợp</th>
                            </tr>
                            <tr>
                                <td class="text-right"><?=currency($v['thu_temp'])?></td>
                                <td class="text-right"><?=currency($v['thu'] - $v['thu_temp'])?></td>
                                <td class="text-right"><?=currency($v['thu'])?></td>
                            </tr>
                            <tr>
                                <td class="text-right"><?=currency($v['chi_temp'])?></td>
                                <td class="text-right"><?=currency($v['chi'] - $v['chi_temp'])?></td>
                                <td class="text-right"><?=currency($v['chi'])?></td>
                            </tr>
                            <tr>
                                <td class="text-right"><?=currency($v['tong_temp'])?></td>
                                <td class="text-right"><?=currency($v['tong'] - $v['tong_temp'])?></td>
                                <td class="text-right"><?=currency($v['tong'])?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach ?>
    </div>
</div>

<script type="text/javascript">
    pageScroll('<?=$pageScrollTarget?>', -50);
    function toggle_collapse_table_detail(target) {
        const container = $(target).parent().parent().find(".table-summarize-container");
        const toogler = $(container).attr("aria-expanded") === "false"
        ? $("<span>").addClass("glyphicon glyphicon-triangle-top")
        : $("<span>").addClass("glyphicon glyphicon-triangle-bottom");

        container.collapse("toggle");
        $(target).children("div").html(toogler);
    }
</script>
