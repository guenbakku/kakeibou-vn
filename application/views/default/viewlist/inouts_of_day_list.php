<div class="panel panel-default">
    <?php if ($total_items > 0): ?>
        <?php for ($i=0; $i<$total_items; $i++): ?>
            <?php if ($i==0 || $list[$i]['date'] != $list[$i-1]['date']): ?>
                <div class="panel-heading"><strong><?=$list[$i]['date']?> (<?=day_of_week($list[$i]['date'])?>)</strong></div>
                <div class="list-group">
            <?php endif ?>
                    <!-- Item -->
                    <a class="list-group-item" href="<?=base_url()?>inout/edit/<?=$list[$i]['iorid']?>">
                        <div style="padding-right:15px; position:absolute; right:0px">
                            <span class="glyphicon glyphicon-menu-right"></span>
                        </div>
                        <div class="row">
                            <div class="col-xs-7">
                                <div><?=$list[$i]['category']?></div>
                                <div class="small text-muted"><em><?=$list[$i]['memo']?></em></div>
                            </div>
                            <div class="col-xs-4 text-right">
                                <div class="<?=$list[$i]['inout_type']=='Thu'? 'text-blue' : 'text-red'?>"><?=currency($list[$i]['amount'])?></div>
                                <div class="label <?=$list[$i]['player_label']?>"><?=$list[$i]['player']?></div><br>
                            </div>
                        </div>
                    </a>
            <?php if ($i==$total_items-1 || $list[$i]['date'] != $list[$i+1]['date']): ?>
                </div>
            <?php endif ?>
        <?php endfor ?>
    <?php else: ?>
        <div class="panel-body">
            Không tìm thấy dữ liệu
        </div>
    <?php endif ?>
</div>