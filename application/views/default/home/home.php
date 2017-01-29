<?php 
    $today = date('Y-m-d');
?>
<div class="container">
    <div class="well well-sm">
        <?=day_of_week($today) . ', ' . $today?>
    </div>
    
    <?php // Chỉ hiện bảng Chi thực tế/Dự định nếu có setting "Dự định chi tháng này" ?>
    <?php if ($liquidOutgoStatus['month'][1] > 0): ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <strong>Chi thực tế / Dự định tháng này</strong><br>
            <span class="small text-muted"><em>(Không tính các khoản chi cố định)</em></span>
        </div>
        <table class="table table-bordered">
            <tr>
                <th style="width:33.3%">
                    <a href="<?=$url['inouts_of_today']?>">Hôm nay</a>
                </th>
                <td class="text-center">
                    <div class="progress" style="margin-bottom:0">
                        <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="<?=$liquidOutgoStatus['today'][2]?>" aria-valuemin="0" aria-valuemax="100" style="width: <?=$liquidOutgoStatus['today'][1]>0? $liquidOutgoStatus['today'][2] : 100?>%">
                            <span><?=currency($liquidOutgoStatus['today'][0], false)?> / <?=currency($liquidOutgoStatus['today'][1], false)?> (<?=$liquidOutgoStatus['today'][2]?>%)</span>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th>
                    <a href="<?=$url['summary_this_month']?>">Tháng này</a>
                </th>
                <td class="text-center">
                    <div class="progress" style="margin-bottom:0">
                        <div class="progress-bar progress-bar-info" role="progressbar" aria-valuenow="<?=$liquidOutgoStatus['month'][2]?>" aria-valuemin="0" aria-valuemax="100" style="width: <?=$liquidOutgoStatus['month'][2]?>%">
                            <span><?=currency($liquidOutgoStatus['month'][0], false)?> / <?=currency($liquidOutgoStatus['month'][1], false)?> (<?=$liquidOutgoStatus['month'][2]?>%)</span>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <?php endif ?>

    <div class="panel panel-default">
        <div class="panel-heading">
            <strong>Thu chi tháng này</strong>
        </div>
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
        </table>
    </div>
    
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