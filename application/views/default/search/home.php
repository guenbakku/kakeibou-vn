<?php 
    echo $this->template->get_view('elements/page-nav');
    echo $this->template->get_view('search/form');
    if ($result === null) return; // Không hiện panel kết quả ở initial display
?>

<script type="text/javascript">
    $(function(){
        var infinite = new Waypoint.Infinite({
            element: $('.infinite-container'),
            items: '.infinite-item',
        })
    });
</script>

<div class="container">
    <?php if ($total_num > 0): ?>
    <p>Tìm được <strong><?=$total_num?></strong> kết quả</p>
    <?php endif ?>
    <div class="infinite-container">
        <?php if ($current_num > 0): ?>
            <?php for ($i=0; $i<$current_num; $i++): ?>
                <?php if ($i==0 || $result[$i]['date'] != $result[$i-1]['date']): ?>
                    <div class="list-group infinite-item">
                        <div class="list-group-item active">
                            <strong><?=$result[$i]['date']?> (<?=day_of_week($result[$i]['date'])?>)</strong>
                        </div>
                <?php endif ?>
                        <!-- Item -->
                        <a class="list-group-item" href="<?=sprintf($url['edit'], $result[$i]['id'])?>">
                            <div style="padding-right:15px; position:absolute; right:0px">
                                <span class="glyphicon glyphicon-menu-right"></span>
                            </div>
                            <div class="row">
                                <div class="col-xs-7" style="padding-right:0">
                                    <div><?=$result[$i]['category']?></div>
                                    <div class="small text-muted"><span class="fa <?=$result[$i]['account_icon']?>"></span> <em><?=$result[$i]['memo']?></em></div>
                                </div>
                                <div class="col-xs-4 text-right">
                                    <div class="<?=$result[$i]['inout_type']=='Thu'? 'text-income' : 'text-outgo'?>"><?=currency($result[$i]['amount'])?></div>
                                    <div class="label <?=$result[$i]['player_label']?>"><?=$result[$i]['player']?></div>
                                </div>
                            </div>
                        </a>
                <?php if ($i==$current_num-1 || $result[$i]['date'] != $result[$i+1]['date']): ?>
                    </div>
                <?php endif ?>
            <?php endfor ?>
        <?php else: ?>
            <div class="list-group">
                <span class="list-group-item"><?=Consts::ERR_NOT_FOUND?></span>
            </div>
        <?php endif ?>
    </div>
    <?php if ($url['next_page'] !== null): ?>
    <a class="infinite-more-link sr-only" href="<?=$url['next_page']?>">Trang tiếp</a>
    <?php endif ?>
</div>
