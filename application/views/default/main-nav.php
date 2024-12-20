<script type="text/javascript">
    $(function(){
        $('.navigation').navigation({'defaultIndex':0});
        $('#inout-dropdown-menu').navigation();
    });
</script>

<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container" style="padding-left:0">
        <div class="navbtn-group navigation">
            <a href="<?= base_url(); ?>"><span class="glyphicon glyphicon-home" style="padding:0 5px"></span></a>
            <?php if ($this->auth->is_authenticated()) { ?>
            <span class="dropdown" href="<?= base_url(); ?>inout/"> <!-- For navigation color -->
                <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">
                    <span class="glyphicon glyphicon-edit" style="padding:0 5px">
                </a>
                <ul id="inout-dropdown-menu" class="dropdown-menu">
                    <?php $i = 0; ?>
                    <?php foreach (Inout_model::$CASH_FLOW_NAMES as $key => $item) { ?>
                        <li href="<?= base_url('inout/add/'.$key); ?>">
                            <a href="<?= base_url('inout/add/'.$key); ?>"><?= ++$i; ?>. <?= $item[0]; ?></a>
                        </li>
                    <?php } ?>
                </ul>
            </span>
            <a href="<?= base_url('timeline/'); ?>"><span class="glyphicon glyphicon-stats" style="padding:0 5px"></span></a>
            <a href="<?= base_url('setting/'); ?>"><span class="glyphicon glyphicon-cog" style="padding:0 5px"></span></a>
            <a href="<?= base_url('search/'); ?>"><span class="glyphicon glyphicon-search" style="padding:0 5px"></span></a>
            <?php } ?>
        </div>
        <div class="currency">
            <span><?= settings('currency'); ?></span>
        </div>
    </div>
</nav>
