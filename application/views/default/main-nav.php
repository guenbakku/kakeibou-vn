<script type="text/javascript">
    navigation('#mainNav');
</script>

<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container fluid" style="padding-left:0">
        <div class="navbtn-group" id="mainNav">
            <a href="<?=base_url()?>"><span class="glyphicon glyphicon-home" style="padding:0 5px"></span></a>
            <a href="<?=base_url()?>inout/"><span class="glyphicon glyphicon-edit" style="padding:0 5px"></a>
            <a href="<?=base_url()?>summary/"><span class="glyphicon glyphicon-th-list" style="padding:0 5px"></span></a>
            <a href="<?=base_url()?>setting/"><span class="glyphicon glyphicon-cog" style="padding:0 5px"></span></a>
        </div>
        <?php if ($this->login_model->isLogin()):?>
            <span class="navbar-text pull-right">
                <a onclick="logout(); return false" href="">
                    <?=$this->login_model->getInfo('fullname')?> 
                    (Logout)
                </a>
            </span>
            <?php echo form_open(base_url().'user/dologout', array('id' => 'logOut', 'class' => 'pull-right')) ?>
            </form>
            
            <script type="text/javascript">               
                function logout(){$('form#logOut').submit()}
            </script>
        <?php endif ?>
    </div>
</nav>