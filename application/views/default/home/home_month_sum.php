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
            <td class="text-right"><?=currency(element('thu_temp', $month_sum, 0))?></td>
            <td class="text-right"><?=currency(element('thu', $month_sum, 0) - element('thu_temp', $month_sum, 0))?></td>
            <td class="text-right"><?=currency(element('thu', $month_sum, 0))?></td>
        </tr>
        <tr>
            <td class="text-right"><?=currency(element('chi_temp', $month_sum, 0))?></td>
            <td class="text-right"><?=currency(element('chi', $month_sum, 0) - element('chi_temp', $month_sum, 0))?></td>
            <td class="text-right"><?=currency(element('chi', $month_sum, 0))?></td>
        </tr>
        <tr>
            <td class="text-right"><?=currency(element('tong_temp', $month_sum, 0))?></td>
            <td class="text-right"><?=currency(element('tong', $month_sum, 0) - element('tong_temp', $month_sum, 0))?></td>
            <td class="text-right"><?=currency(element('tong', $month_sum, 0))?></td>
        </tr>
    </table>
</div>
