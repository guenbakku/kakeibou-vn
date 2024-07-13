<footer class="footer">
    <div class="container">
        <span class="text-muted">
            BH Cash Book v<?= Consts::VERSION; ?> &copy; <?= date('Y'); ?>
            <br>
            <?php if ($this->auth->is_authenticated()) { ?>
                <a onclick="logout(); return false" href="">
                    <?= $this->auth->user('fullname'); ?> 
                    (Logout)
                </a>
                
                <?= form_open(base_url().'user/logout', ['id' => 'logout', 'class' => 'pull-right']); ?></form>
                
                <script type="text/javascript">               
                    function logout(){$('form#logout').submit()}
                </script>
            <?php } ?>
        </span>
    </div>
    
</footer>