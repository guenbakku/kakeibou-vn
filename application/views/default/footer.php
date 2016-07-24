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
                <?php echo form_open(base_url().'user/dologout', array('id' => 'logOut', 'class' => 'pull-right')) ?>
                </form>
                
                <script type="text/javascript">               
                    function logout(){$('form#logOut').submit()}
                </script>
            <?php endif ?>
        </span>
    </div>
    
</footer>