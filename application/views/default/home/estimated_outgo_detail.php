<div class="sr-only outgo-status">
    <table style="min-width:160px">
        <tr>
            <td style="width:35%">Dự định:</td> 
            <td style="width:35%" class="text-right"><?=currency($estimated, false)?></td>
            <td style="width:30%" class="text-right text-muted"><?=$estimated_percent?>%</td>
        </tr>
        <tr>
            <td>Đã chi:</td> 
            <td class="text-right"><?=currency($elapsed, false)?></td>
            <td class="text-right text-muted"><?=$elapsed_percent?>%</td>
        </tr>
        <tr>
            <td>Còn lại:</td> 
            <td class="text-right"><?=currency($remain, false)?></td>
            <td class="text-right text-muted"><?=$remain_percent?>%</td>
        </tr>
    </table>
</div>
<div class="progress" style="margin-bottom:0" data-toggle="popover">
    <div class="progress-bar progress-bar-info" role="progressbar"
        aria-valuenow="<?=$remain_percent?>" 
        aria-valuemin="0" aria-valuemax="100" 
        style="width: <?=$remain_percent?>%">
        <span><?=currency($remain, false)?></span>
    </div>
</div>