<div class="container">
    <h4 class="text-center"><?=$date?></h4>
    <div class="well">
        <?=form_open($form_url, array('method'=>'get', 'id' => 'addCashFlow', 'class' => 'form-horizon'))?>
            <div class="row">
                <div class="col-xs-8">
                    <?=form_dropdown(
                        'account', 
                        $select['accounts'] + array('0' => 'Thực thu chi'), 
                        $account, 
                        array(
                            'class' => 'form-control',
                        )
                    )?>
                </div>
                <div class="col-xs-4">
                    <button type="submit" class="btn btn-primary btn-sm">Xem</button>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-8">
                    <?=form_dropdown(
                        'player', 
                        $select['players'] + array('0' => 'Tất cả'), 
                        $player, 
                        array(
                            'class' => 'form-control',
                        )
                    )?>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="container">
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
                                    <?php if ($list[$i]['inout_type']=='Thu'):?>
                                        <div class="text-blue">+<?=number_format($list[$i]['amount'])?></div>
                                    <?php else: ?>
                                        <div class="text-red">-<?=number_format($list[$i]['amount'])?></div>
                                    <?php endif ?>
                                    <div class="label <?=$list[$i]['player_label']?>"><?=$list[$i]['player']?></div><br>
                                </div>
                            </div>
                        </a>
                <?php if ($i==0 || $list[$i]['date'] != $list[$i-1]['date']): ?>
                    </div>
                <?php endif ?>
            <?php endfor ?>
        <?php else: ?>
            <div class="panel-body">
                Không có dữ liệu
            </div>
        <?php endif ?>

    </div>
</div>