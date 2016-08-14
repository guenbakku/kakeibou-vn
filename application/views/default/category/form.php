<?php
$disabled_attr = $this->router->fetch_method() == 'edit'
                 ? array('disabled' => true)
                 : array();
?>

<div class="container">
    <?php echo form_open($form_url, array('class' => 'form-vertical'))?>
        <div class="panel panel-default">
            <div class="panel-heading"><strong><?=$title?></strong></div>
            <div class="panel-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-xs-6">
                            <label>Loại:</label>
                            <?=form_dropdown(
                                $field_name = 'inout_type_id', 
                                $select['inout_types'], 
                                set_value($field_name, 0), 
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
                
                <div class="form-group">
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
                
                <button type="submit" class="btn btn-primary">Nhập</button>
                <?php if ($this->router->fetch_method() == 'edit'): ?>
                    <button type="button" class="btn btn-danger pull-right" onclick="del_category()">Xóa</button>
                <?php endif ?>
                
            </div>
        </div>
    </form>
</div>

<?php if ($this->router->fetch_method() == 'edit'): ?>
    <script type="text/javascript">
        function del_category(){
            if (confirm('Muốn xóa danh mục này?')){
                $('#delCategory').submit();
            }
        }
    </script>
    
    <?php echo form_open($del_url, array('id' => 'delCategory', 'class' => 'form-vertical sr-only'))?>
    </form>
<?php endif ?> 