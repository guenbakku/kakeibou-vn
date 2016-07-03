<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading"><strong>Ghi chép thu chi</strong></div>
        <div class="list-group">
            <?php $i=0 ?>
            <?php foreach (Inout_model::$CASH_FLOW_NAMES as $key => $item): ?>
            <a class="list-group-item" href="add/<?=$key?>"><span class="glyphicon glyphicon-menu-right pull-right"></span><?=(++$i)?>. <?=$item[0]?></a>
            <?php endforeach ?>
       </div>
    </div>
</div>