<?php
$disabled_attr = $this->router->fetch_method() == 'edit'
                 ? array('disabled' => true)
                 : array();
?>

<?=$this->template->get_view('elements/page-nav')?>
<div class="container">
    <?php echo form_open($url['form'], array('class' => 'form-vertical'))?>
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-xs-6">
                            <label>Loại:</label>
                            <?=form_dropdown(
                                $field_name = 'inout_type_id', 
                                $select['inout_types'], 
                                set_value($field_name, 1), 
                                array(
                                    'class' => 'form-control',
                                ) + $disabled_attr
                            )?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-xs-12">
                            <label>Tên danh mục:</label>
                            <?=form_input(
                                array(
                                    'name' => $field_name = 'name',
                                    'type' => 'text',
                                ),
                                set_value($field_name, null, false),
                                array(
                                    'class' => 'form-control',
                                )
                            )?>
                        </div>
                    </div>
                </div>
                <button type="button" onClick="Cashbook.submitbutton(this, 'submit')" class="btn btn-primary"><?=Consts::LABEL['submit']?></button>
                <?php if ($this->router->fetch_method() == 'edit'): ?>
                    <button type="button" onClick="Cashbook.submitbutton(this, 'delete')" class="btn btn-danger pull-right"><?=Consts::LABEL['delete']?></button>
                <?php endif ?>
                
            </div>
        </div>
    </form>
</div>