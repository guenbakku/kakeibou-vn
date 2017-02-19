<?=$this->template->get_view('elements/viewlist/header_ymd')?>
<?=$this->template->get_view('elements/viewlist/button_group')?>

<div class="container">
    <div class="well well-sm">
        <?=form_open($url['subForm'], array('method'=>'get', 'id' => 'subForm', 'class' => 'form-horizon'))?>
            <div class="row">
                <div class="col-xs-6">
                    <label>Tài khoản</label>
                    <?=form_dropdown(
                        'account', 
                        $select['accounts'] + array('0' => 'Thực thu chi'), 
                        $account_id, 
                        array(
                            'class' => 'form-control submit-on-change',
                        )
                    )?>
                </div>
                <div class="col-xs-6">
                    <label>Phụ trách</label>
                    <?=form_dropdown(
                        'player', 
                        $select['players'] + array('0' => 'Tất cả'), 
                        $player_id, 
                        array(
                            'class' => 'form-control submit-on-change',
                        )
                    )?>
                </div>
            </div>
        </form>
    </div>
        
    <div class="panel panel-default">
        <?php if ($total_items > 0): ?>
            <?php for ($i=0; $i<$total_items; $i++): ?>
                <?php if ($i==0 || $list[$i]['date'] != $list[$i-1]['date']): ?>
                    <div class="panel-heading"><strong><?=$list[$i]['date']?> (<?=day_of_week($list[$i]['date'])?>)</strong></div>
                    <div class="list-group">
                <?php endif ?>
                        <!-- Item -->
                        <a class="list-group-item" href="<?=sprintf($url['editTemplate'], $list[$i]['id'])?>">
                            <div style="padding-right:15px; position:absolute; right:0px">
                                <span class="glyphicon glyphicon-menu-right"></span>
                            </div>
                            <div class="row">
                                <div class="col-xs-7" style="padding-right:0">
                                    <div><?=$list[$i]['category']?></div>
                                    <div class="small text-muted"><span class="fa <?=Account_model::$ICONS[$list[$i]['account_id']]?>"></span> <em><?=$list[$i]['memo']?></em></div>
                                </div>
                                <div class="col-xs-4 text-right">
                                    <div class="<?=$list[$i]['inout_type']=='Thu'? 'text-income' : 'text-outgo'?>"><?=currency($list[$i]['amount'])?></div>
                                    <div class="label <?=$list[$i]['player_label']?>"><?=$list[$i]['player']?></div>
                                </div>
                            </div>
                        </a>
                <?php if ($i==$total_items-1 || $list[$i]['date'] != $list[$i+1]['date']): ?>
                    </div>
                <?php endif ?>
            <?php endfor ?>
        <?php else: ?>
            <div class="panel-body">
                <?=Consts::ERR_NOT_FOUND?>
            </div>
        <?php endif ?>
    </div>
</div>