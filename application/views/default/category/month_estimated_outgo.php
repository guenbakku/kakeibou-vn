<?=$this->template->get_view('elements/page-nav')?>
<div class="container">
    <?php echo form_open($url['form'], array('class' => 'form-vertical'))?>
        <div class="panel panel-default">
            <div class="panel-body">
                <?php foreach ($categories as $i => $category): ?>
                <div class="form-group">
                    <?=form_input(
                        [
                            'name' => $field_name = sprintf('categories[%d][id]', $i),
                            'type' => 'hidden',
                        ],
                        $category['id']
                    )?>
                    <?=form_input(
                        [
                            'name' => $field_name = sprintf('categories[%d][name]', $i),
                            'type' => 'hidden',
                        ],
                        $category['name']
                    )?>
                    <div class="row">
                        <div class="col-xs-9">
                            <div class="row">
                                <div class="col-xs-12">
                                    <label><?=$category['name']?></label>
                                </div>
                                <div class="col-xs-12">
                                    <div class="input-group">
                                        <?=form_input(
                                            [
                                                'name' => $field_name = sprintf('categories[%d][month_estimated_amount]', $i),
                                                'type' => 'number',
                                                'pattern' => '\d*',
                                            ],
                                            set_value($field_name, null),
                                            [
                                                'class' => 'form-control',
                                            ]
                                        )?>
                                        <span class="input-group-addon">¥</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-3">
                            <div class="row">
                                <div class="col-xs-12">
                                    <label>Cố định</label>
                                </div>
                                <div class="col-xs-12">
                                    <?=form_checkbox(
                                        array(
                                            'name'      => $field_name = sprintf('categories[%d][is_month_fixed_money]', $i),
                                            'value'     => '1',
                                            'disabled'  => true,
                                            'checked'   => (bool)set_value($field_name, false),
                                        )
                                    )?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach ?>
                
                <button type="button" onClick="Cashbook.submitbutton(this, 'submit')" class="btn btn-primary"><?=Consts::LABEL['submit']?></button>
            </div>
        </div>
    </form>
</div>