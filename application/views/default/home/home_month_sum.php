<div class="panel panel-default">
    <div class="panel-heading">
        <a class="pull-right" href="<?=$url['summaryThisYear']?>">
            <span class="glyphicon glyphicon-option-horizontal"></span>
        </a>
        <strong>Thu chi tháng này</strong>
    </div>
    <table class="table table-bordered last-row-is-total">
        <tr>
            <th style="width:33.3%" class="text-center">Danh nghĩa</th>
            <th style="width:33.3%" class="text-center">Thực tế</th>
            <th style="width:33.3%" class="text-center">Tổng hợp</th>
        </tr>
        <tr>
            <td class="text-right"><?=currency($month_sum['thu_temp'])?></td>
            <td class="text-right"><?=currency($month_sum['thu'] - $month_sum['thu_temp'])?></td>
            <td class="text-right"><?=currency($month_sum['thu'])?></td>
        </tr>
        <tr>
            <td class="text-right"><?=currency($month_sum['chi_temp'])?></td>
            <td class="text-right"><?=currency($month_sum['chi'] - $month_sum['chi_temp'])?></td>
            <td class="text-right"><?=currency($month_sum['chi'])?></td>
        </tr>
        <tr>
            <td class="text-right"><?=currency($month_sum['tong_temp'])?></td>
            <td class="text-right"><?=currency($month_sum['tong'] - $month_sum['tong_temp'])?></td>
            <td class="text-right"><?=currency($month_sum['tong'])?></td>
        </tr>
    </table>
</div>
