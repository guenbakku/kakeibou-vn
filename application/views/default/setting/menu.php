<?=$this->template->get_view('elements/page-nav')?>

<div class="container">
    <div class="panel panel-default">
        <div class="list-group">
            <?php foreach ($this->setting_model->get(null, 'name') as $key => $item): ?>
            <a class="list-group-item" href="<?=base_url(['setting', 'edit', $key])?>"><span class="glyphicon glyphicon-menu-right pull-right"></span><?=$item?></a>
            <?php endforeach ?>
            <a class="list-group-item" href="<?=base_url(['setting', 'category'])?>"><span class="glyphicon glyphicon-menu-right pull-right"></span>Quản lý danh mục</a>
       </div>
    </div>
</div>
    