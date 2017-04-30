<?=$this->template->get_view('elements/page-nav')?>
<?=$this->template->get_view('search/form')?>

<?php if ($result !== null): ?>
    <div class="container">
        <?php if ($total_num > 0): ?>
            <p>Tìm được <strong><?=$total_num?></strong> kết quả</p>
        <?php endif ?>
        <div class="panel panel-default">
            <?php if ($page_num > 0): ?>
                <?php for ($i=0; $i<$page_num; $i++): ?>
                    <?php if ($i==0 || $result[$i]['date'] != $result[$i-1]['date']): ?>
                        <div class="panel-heading"><strong><?=$result[$i]['date']?> (<?=day_of_week($result[$i]['date'])?>)</strong></div>
                        <div class="list-group">
                    <?php endif ?>
                            <!-- Item -->
                            <a class="list-group-item" href="<?=sprintf($url['edit'], $result[$i]['id'])?>">
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
                    <?php if ($i==$page_num-1 || $result[$i]['date'] != $result[$i+1]['date']): ?>
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
<?php endif ?>
