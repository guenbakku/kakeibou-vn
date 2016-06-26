<script type="text/javascript">
    navigation('#mainNav');
</script>

<nav class="navbar navbar-default">
    <div class="container" style="padding-left:0">
        <div class="navbtn-group" id="mainNav">
            <a href="<?=base_url()?>"><span class="glyphicon glyphicon-home" style="padding:0 5px"></span></a>
            <a href="<?=base_url()?>inout/"><span class="glyphicon glyphicon-edit" style="padding:0 5px"></a>
            <a href="<?=base_url()?>summary/"><span class="glyphicon glyphicon-th-list" style="padding:0 5px"></span></a>
        </div>
        <?php if ($this->login_model->isLogin()):?>
            <span class="navbar-text pull-right">
                <a href="<?=base_url()?>user/dologout">
                    <?=$this->login_model->getInfo('fullname')?> 
                    (Logout)
                </a>
            </span>
        <?php endif ?>
    </div>
</nav>