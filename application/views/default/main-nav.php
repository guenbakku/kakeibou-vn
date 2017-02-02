<script type="text/javascript">
    $(function(){
        $('.navigation').navigation({'defaultIndex':0});
    });
</script>

<nav class="navbar navbar-default navbar-fixed-top">
    <div class="container fluid" style="padding-left:0">
        <div class="navbtn-group navigation">
            <a href="<?=base_url()?>"><span class="glyphicon glyphicon-home" style="padding:0 5px"></span></a>
            <?php if($this->login_model->isLogin()):?>
            <span class="dropdown" href="<?=base_url()?>inout/"> <!-- For navigation color -->
                <a class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">
                    <span class="glyphicon glyphicon-edit" style="padding:0 5px">
                </a>
                <ul class="dropdown-menu">
                    <?php $i=0 ?>
                    <?php foreach (Inout_model::$CASH_FLOW_NAMES as $key => $item): ?>
                        <li><a href="<?=base_url('inout/add/'.$key)?>"><?=(++$i)?>. <?=$item[0]?></a></li>
                    <?php endforeach ?>
                </ul>
            </span>
            <a href="<?=base_url('viewlist/')?>"><span class="glyphicon glyphicon-th-list" style="padding:0 5px"></span></a>
            <a href="<?=base_url('chart/')?>"><span class="glyphicon glyphicon-equalizer" style="padding:0 5px"></span></a>
            <a href="<?=base_url('setting/')?>"><span class="glyphicon glyphicon-cog" style="padding:0 5px"></span></a>
            <a href="<?=base_url('search/')?>"><span class="glyphicon glyphicon-search" style="padding:0 5px"></span></a>
            <?php endif ?>
        </div>
    </div>
</nav>