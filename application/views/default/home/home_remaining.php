<div class="panel panel-default">
    <div class="panel-heading"><strong>Tiền còn lại</strong></div>
    <table class="table table-bordered last-row-is-total">
        <tr>
            <th style="width:34%"><br></th>
            <th class="text-center">Hiện tại</th>
            <th class="text-center" style="width:33.3%">Tương lai</th>
        </tr>
        <?php foreach ($remaining as $k => $v) { ?>
            <tr>
                <th><?= $k; ?></th>
                <td class="text-right"><?= currency($v[0]); ?></td>
                <td class="text-right"><?= currency($v[1]); ?></td>
            </tr>
        <?php } ?>
    </table>
</div>
