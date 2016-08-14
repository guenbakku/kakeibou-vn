<script type="text/javascript">
$(function(){
    $('.toggle').click(function(){
        $('.toggle-target').toggleClass('hide');        
    });
});
</script>

<div class="container">
    <div class="panel panel-default">
        <a class="panel-heading" href="<?=$url['month_inout_list']?>" style="display:block">
            <strong><span class="glyphicon glyphicon-menu-right pull-right"></span>Thu chi tháng này (<?=date('Y-m')?>)</strong>
        </a>
        <table class="table table-bordered" style="border-bottom:1px solid; border-color:inherit">
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
        </table>
        
        <?php if ($liquidOutgoStatus['month'][1] > 0): ?>
        <div class="panel-heading"">
            <strong>Tỷ lệ Chi thực tế / Dự định</strong><br>
            <span class="small text-muted"><em>(Không tính các khoản chi cố định)</em></span>
        </div>
        <table class="table table-bordered">
            <tr>
                <th style="width:33.3%">Hôm nay</th>
                <td class="text-center">
                    <div class="progress" style="margin-bottom:0">
                        <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="<?=$liquidOutgoStatus['today'][2]?>" aria-valuemin="0" aria-valuemax="100" style="width: <?=$liquidOutgoStatus['today'][1]>0? $liquidOutgoStatus['today'][2] : 100?>%">
                            <span><?=currency($liquidOutgoStatus['today'][0], false)?> / <?=currency($liquidOutgoStatus['today'][1], false)?> (<?=$liquidOutgoStatus['today'][2]?>%)</span>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th>Tháng này</th>
                <td class="text-center">
                    <div class="progress" style="margin-bottom:0">
                        <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="<?=$liquidOutgoStatus['month'][2]?>" aria-valuemin="0" aria-valuemax="100" style="width: <?=$liquidOutgoStatus['month'][2]?>%">
                            <span><?=currency($liquidOutgoStatus['month'][0], false)?> / <?=currency($liquidOutgoStatus['month'][1], false)?> (<?=$liquidOutgoStatus['month'][2]?>%)</span>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        <?php endif ?>
        
    </div>
</div>

<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading"><strong>Tiền còn lại</strong></div>
        <table class="table table-bordered">
            <tr>
                <th style="width:34%"><br></th>
                <th class="text-center">Hiện tại</th>
                <th class="text-center" style="width:33.3%">Tương lai</th>
            </tr>
            <?php foreach ($remaining as $k => $v): ?>
            <tr>
                <th><?=$k?></th>
                <td class="text-right"><?=currency($v[0])?></td>
                <td class="text-right"><?=currency($v[1])?></td>
            </tr>
            <?php endforeach ?>
        </table>
    </div>
</div>