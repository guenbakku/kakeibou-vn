<script type="text/javascript">
    navigation('#mainNav');
</script>

<nav class="navbar navbar-default">
    <div class="container" style="padding-left:0">
        <div class="navbtn-group" id="mainNav">
            <a href="<?=base_url()?>home/"><span class="glyphicon glyphicon-home"></span></a>
            <a href="<?=base_url()?>record/">Thêm</a>
            <a href="<?=base_url()?>summary/">Thống kê</a>
        </div>
        <?php if ($this->login_model->isLogin()):?>
            <span class="navbar-text pull-right"><a href="<?=base_url()?>login/dologout">Logout</a></span>
        <?php endif ?>
    </div>
</nav>