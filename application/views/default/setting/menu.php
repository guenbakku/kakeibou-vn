<?= $this->template->get_view('elements/page-nav'); ?>

<div class="container">
    <div class="panel panel-default">
        <div class="list-group">
            <a class="list-group-item" href="<?= base_url(['setting', 'category', 'month_estimated_outgo']); ?>"><span class="glyphicon glyphicon-menu-right pull-right"></span>Dự định chi tháng này</a>
            <a class="list-group-item" href="<?= base_url(['setting', 'category']); ?>"><span class="glyphicon glyphicon-menu-right pull-right"></span>Quản lý danh mục</a>
            <a class="list-group-item" href="<?= base_url(['setting', 'account']); ?>"><span class="glyphicon glyphicon-menu-right pull-right"></span>Quản lý tài khoản</a>
       </div>
    </div>
    <div class="panel panel-default">
        <div class="list-group">
            <a class="list-group-item" href="<?= base_url(['setting', 'user', 'edit', 'info']); ?>"><span class="glyphicon glyphicon-menu-right pull-right"></span>Thông tin cá nhân</a>
            <a class="list-group-item" href="<?= base_url(['setting', 'user', 'edit', 'password']); ?>"><span class="glyphicon glyphicon-menu-right pull-right"></span>Thay đổi mật khẩu</a>
       </div>
    </div>
</div>
    