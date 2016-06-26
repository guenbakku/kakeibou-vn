<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading"><strong>Thay đổi thiết đặt</strong></div>
        <div class="list-group">
            <?php $i=0 ?>
            <?php foreach ($this->setting_model->get(null, 'name') as $key => $item): ?>
            <a class="list-group-item" href="edit/<?=$key?>"><span class="glyphicon glyphicon-menu-right pull-right"></span><?=(++$i)?>. <?=$item?></a>
            <?php endforeach ?>
       </div>
    </div>
</div>
    