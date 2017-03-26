<table style="min-width:160px">
    <tr>
        <td style="width:35%">Dự định:</td> 
        <td style="width:35%" class="text-right"><?=currency($estimated, false)?></td>
        <td style="width:30%" class="text-right text-muted small"><?=$estimated_percent?>%</td>
    </tr>
    <tr>
        <td>Đã chi:</td> 
        <td class="text-right"><?=currency($elapsed, false)?></td>
        <td class="text-right text-muted small"><?=$elapsed_percent?>%</td>
    </tr>
    <tr>
        <td>Còn lại:</td> 
        <td class="text-right"><?=currency($remain, false)?></td>
        <td class="text-right text-muted small"><?=$remain_percent?>%</td>
    </tr>
</table>