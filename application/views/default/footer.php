<footer class="footer">
    <div class="container">
        <span class="text-muted">
            BH Cash Book V<?=Constants::VERSION?> &copy; <?=date('Y')?>
            <br>
            <?php if ($this->login_model->isLogin()):?>
                <a onclick="logout(); return false" href="">
                    <?=$this->login_model->getInfo('fullname')?> 
                    (Logout)
                </a>
                
                <?php echo form_open(base_url().'user/logout', array('id' => 'logout', 'class' => 'pull-right')) ?></form>
                
                <script type="text/javascript">               
                    function logout(){$('form#logout').submit()}
                </script>
            <?php endif ?>
        </span>
    </div>
    
</footer>