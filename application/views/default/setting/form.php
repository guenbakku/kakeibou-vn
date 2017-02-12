<?=$this->template->get_view('elements/page-nav')?>
<div class="container">           
    <?php echo form_open($url['form'], array('id' => 'editSetting', 'class' => 'form-vertical'))?>
        <div class="panel panel-default">
            <div class="panel-body">
                <?php if (!is_array($setting['value'])): ?>
                    <div class="form-group">
                        <?=form_input(
                                $setting['item'],
                                $setting['value'],
                                array(
                                    'class' => 'form-control',
                                )
                            )?>
                    </div>
                <?php else: ?>
                    <?php foreach ($setting['value'] as $k => $v): ?>
                        <div class="form-group">
                            <label><?=$k?></label>
                            <?=form_input(
                                    $setting['item'].'['.$k.']',
                                    $v,
                                    array(
                                        'class' => 'form-control',
                                    )
                                )?>
                        </div>
                    <?php endforeach ?>
                <?php endif ?>
                <button type="submit" class="btn btn-primary">Nhập</button>
            </div>
        </div>
    </form>
</div>
    
    