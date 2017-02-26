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
                
                <div id="month_fixed_money" class="form-group">
                    <label>
                        <input type="hidden" value="0" name="<?=$field_name = 'month_fixed_money'?>">
                        <?=form_checkbox(
                            array(
                                'name'      => $field_name,
                                'value'     => '1',
                                'checked'   => (bool)set_value($field_name, FALSE),
                            )
                        )?>
                        Khoản chi cố định hàng tháng
                    </label>
                </div>
                
                <button type="button" onClick="Cashbook.submitbutton(this, 'submit')" class="btn btn-primary"><?=Consts::LABEL['submit']?></button>
                <?php if ($this->router->fetch_method() == 'edit'): ?>
                    <button type="button" onClick="Cashbook.submitbutton(this, 'delete')" class="btn btn-danger pull-right"><?=Consts::LABEL['delete']?></button>
                <?php endif ?>
                
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">
    
    display_month_fixed_money_checkbox();
    $('select[name=inout_type_id]').change(function(){
        display_month_fixed_money_checkbox();
    });
    
    function display_month_fixed_money_checkbox() {
        if ($('select[name=inout_type_id]').val() == 2){
            $('#month_fixed_money').show();
        }
        else {
            $('#month_fixed_money').hide();
        }
    }
    
</script>