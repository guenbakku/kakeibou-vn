<?=$this->template->get_view('elements/page-nav.php')?>
<?=$this->template->get_view('elements/timeline/header', ['date_element' => 'header_ymd'])?>

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
        
    <?php if ($total_items > 0): ?>
        <?php for ($i=0; $i<$total_items; $i++): ?>
            <?php if ($i==0 || $result[$i]['date'] != $result[$i-1]['date']): ?>
                <div class="list-group">
                    <div class="list-group-item active">
                        <strong><?=$result[$i]['date']?> (<?=day_of_week($result[$i]['date'])?>)</strong>
                    </div>
            <?php endif ?>
                    <!-- Item -->
                    <a class="list-group-item" href="<?=sprintf($url['editTemplate'], $result[$i]['id'])?>">
                        <div style="padding-right:15px; position:absolute; right:0px">
                            <span class="glyphicon glyphicon-menu-right"></span>
                        </div>
                        <div class="row">
                            <div class="col-xs-7" style="padding-right:0">
                                <div><?=$result[$i]['category']?></div>
                                <div class="small text-muted"><span class="fa <?=Account_model::$ICONS[$result[$i]['account_id']]?>"></span> <em><?=$result[$i]['memo']?></em></div>
                            </div>
                            <div class="col-xs-4 text-right">
                                <div class="<?=$result[$i]['inout_type']=='Thu'? 'text-income' : 'text-outgo'?>"><?=currency($result[$i]['amount'])?></div>
                                <div class="label <?=$result[$i]['player_label']?>"><?=$result[$i]['player']?></div>
                            </div>
                        </div>
                    </a>
            <?php if ($i==$total_items-1 || $result[$i]['date'] != $result[$i+1]['date']): ?>
                </div>
            <?php endif ?>
        <?php endfor ?>
    <?php else: ?>
        <div class="list-group">
            <span class="list-group-item"><?=Consts::ERR_NOT_FOUND?></span>
        </div>
    <?php endif ?>
</div>