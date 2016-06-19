<script type="text/javascript">
    navigation('#subNav');
</script>

<div class="container">
    <div id="subNav" class="btn-group btn-group-justified">
        <a class="btn btn-default" href="<?=base_url().$this->router->fetch_class().'/'.$this->router->fetch_method()?>/day">Ngày</a>
        <a class="btn btn-default" href="<?=base_url().$this->router->fetch_class().'/'.$this->router->fetch_method()?>/month">Tháng</a>
        <a class="btn btn-default" href="<?=base_url().$this->router->fetch_class().'/'.$this->router->fetch_method()?>/year">Năm</a>
    </div>
</div>
<br>

<div class="container">
    <div class="panel panel-default">
    
        <?php if (in_array($mode, array('day', 'week', 'month'))): ?>
        <div class="panel-heading">
            <a data-toggle="collapse" data-target="#collapse-target" href="#collapse-target"> 
                Thay đổi <span class="glyphicon glyphicon-triangle-bottom" aria-hidden="true"></span>
            </a>
        </div>
        <div class="panel-collapse collapse" id="collapse-target">
            <div class="panel-heading">
                <?php echo form_open($form_url, array('method'=>'get', 'id' => 'addCashFlow', 'class' => 'form-inline'))?>
                    <div class="form-group">
                        <label class="control-label">Năm</label>
                        <?=form_dropdown(
                            $field_name = 'year', 
                            $select['year'], 
                            $year, 
                            array(
                                'class' => 'form-control',
                            )
                        )?>
                    </div>
                    <?php if ($mode == 'day'): ?>
                    <div class="form-group">
                        <label class="control-label">Tháng</label>
                        <?=form_dropdown(
                            $field_name = 'month', 
                            $select['month'], 
                            $month, 
                            array(
                                'class' => 'form-control',
                            )
                        )?>
                    </div>
                    <?php endif ?>
                    <button type="submit" class="btn btn-primary btn-sm">Xem</button>
                </form>
            </div>
        </div>
        <?php else: ?>
        <div class="panel-heading">
            <br>
        </div>
        <?php endif ?>
        
        <div class="list-group">
            <?php foreach($list as $k => $v) : ?>
            <a class="list-group-item" <?php if (!$v['empty']): ?> href="<?=base_url().'summary/detail'?> <?php endif ?>">
                <div class="row">
                    <div class="col-xs-11">
                        <div class="row">
                            <div class="col-xs-6"><span class="label label-default"><?=$k?></span></div>
                            <div class="col-xs-6 text-right text-blue">+<?=number_format($v['thu'])?></div>
                        </div>
                        <div class="row">
                            <div class="col-xs-6"><?=number_format($v['tong'])?></div>
                            <div class="col-xs-6 text-right text-red">-<?=number_format($v['chi'])?></div>
                        </div>
                    </div>
                    <?php if(!$v['empty']): ?>
                    <div class="pull-right" style="padding-right:15px; position:absolute; right:0px">
                        <span class="glyphicon glyphicon-menu-right"></span>
                    </div>
                    <?php endif ?>
                </div>
            </a>
            <?php endforeach ?>
        </div>
    </div>
</div>