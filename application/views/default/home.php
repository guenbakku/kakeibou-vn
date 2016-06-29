<script type="text/javascript">
$(function(){
    $('.toggle').click(function(){
        $('.toggle-target').toggleClass('hide');        
    });
});
</script>

<div class="container">
    <div class="panel panel-default">
        <a class="panel-heading" href="<?=base_url()?>summary/viewlist" style="display:block">
            <strong><span class="glyphicon glyphicon-menu-right pull-right"></span> Thu chi trong tháng (<?=date('Y-m')?>)</strong>
        </a>
        <table class="table table-bordered">
            <tr>
                <th style="width:33.3%" class="text-center">Thu</th>
                <th style="width:33.3%" class="text-center">Chi</th>
                <th style="width:33.3%" class="text-center">Chênh lệch</th>
            </tr>
            <tr>
                <td class="text-right"><?=currency($month_sum['thu'])?></td>
                <td class="text-right"><?=currency($month_sum['chi'])?></td>
                <td class="text-right"><?=currency($month_sum['tong'])?></td>
            </tr>
            <tr>
                <th colspan="2" class="text-center">Mỗi ngày có thể tiêu:</th>
                <td class="text-right"><?=currency($day_available_outgo)?></td>
            </tr>
        </table>
    </div>
</div>

<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading"><strong>Tiền còn lại</strong></div>
        <table class="table table-bordered">
            <?php foreach ($remaining as $k => $v): ?>
            <tr>
                <th><?=$k?></th>
                <td style="width:33.3%" class="text-right"><?=currency($v)?></td>
            </tr>
            <?php endforeach ?>
        </table>
    </div>
</div>