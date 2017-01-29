<?php 
// Xóa lựa chọn "Tiền mặt" nếu thao tác là Rút hoặc nạp tiền vào tài khoản
if (in_array($type, array('drawer', 'deposit'))){
    unset($select['accounts'][Inout_model::ACCOUNT_CASH_ID]);
}

// Thêm thuộc tính disabled cho input/select nếu dữ liệu sửa là dữ liệu pair
$disabled_attr = (!empty($pair_id))? array('disabled' => 'true') : array();
?>

<script type="text/javascript">
    $(function(){        
        $(".autocomplete").autocomplete({ 
            source: function(req, resp) {
                $.getJSON("/inout/searchMemo/" + encodeURIComponent(req.term), resp);
            },
            minLength: 2,
        });
    });
</script>

<div class="container">

    <div class="page-nav">
        <div class="row">
            <div class="col-xs-2">
                <a class="btn btn-default btn-sm" href="<?=$url['back']?>">
                    <span class="glyphicon glyphicon-menu-left"></span>
                </a>
            </div>
            <div class="col-xs-10">
                <strong><?=$title?></strong>
            </div>
        </div>
    </div>

    <?php echo form_open($url['form'], array('id' => 'addCashFlow', 'class' => 'form-vertical'))?>
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="form-group">
                    <label>Số tiền:</label>
                    <div class="input-group">
                        <span class="input-group-addon"><?=$inout_type_sign?></span>
                        <?=form_input(
                            array(
                                'name' => $field_name = 'amount',
                                'type' => 'number',
                                'pattern' => '\d*',
                            ),
                            set_value($field_name, null),
                            array(
                                'class' => 'form-control',
                            )
                        )?>
                        <span class="input-group-addon">¥</span>
                    </div>
                </div>
                <div class="form-group">
                    <label>Thời gian:</label>
                    <?=form_input(
                        array(
                            'name' => $field_name = 'date',
                            'type' => 'date',
                        ),
                        set_value($field_name, date('Y-m-d')),
                        array(
                            'class' => 'form-control',
                        )
                    )?>
                </div>
                
                <?php if (in_array($type, array('outgo', 'income'))): ?>
                <div class="form-group">
                    <label>Danh mục:</label>
                    <?=form_dropdown(
                        $field_name = 'category_id', 
                        $select['categories'], 
                        set_value($field_name, null), 
                        array(
                            'class' => 'form-control',
                        )
                    )?>
                </div>
                <?php endif ?>
                
                <?php if (in_array($type, array('outgo', 'income', 'drawer', 'deposit'))): ?>
                <div class="row">
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label>Tài khoản:</label>
                            <?=form_dropdown(
                                $field_name = 'account_id',
                                $select['accounts'], 
                                set_value($field_name, null), 
                                array(
                                    'class' => 'form-control',
                                ) + $disabled_attr
                            )?>
                        </div>
                    </div>
                    <div class="col-xs-6">
                        <div class="form-group">
                            <label>Phụ trách:</label>
                            <?=form_dropdown(
                                $field_name = 'player', 
                                $select['players'], 
                                set_value($field_name, $this->login_model->getInfo('id')), 
                                array(
                                    'class' => 'form-control',
                                )
                            )?>
                        </div>
                    </div>
                </div>
                <?php endif ?>
                
                <?php if (in_array($type, array('handover'))): ?>
                <div class="form-group">
                    <div class="row">
                        <div class="col-xs-6"><label>Chuyển từ:</label></div>
                        <div class="col-xs-6"><label>đến:</label></div>
                    </div>
                    <div class="row">
                        <div class="col-xs-6">
                            <?=form_dropdown(
                                $field_name = 'player[0]', 
                                $select['players'], 
                                set_value($field_name, $this->login_model->getInfo('id')), 
                                array(
                                    'class' => 'form-control',
                                ) + $disabled_attr
                            )?>
                        </div>
                        <div class="col-xs-6">
                            <?=form_dropdown(
                                $field_name = 'player[1]', 
                                $select['players'], 
                                set_value($field_name, 3-$this->login_model->getInfo('id')), 
                                array(
                                    'class' => 'form-control',
                                ) + $disabled_attr
                            )?>
                        </div>
                    </div>
                </div>
                <?php endif ?>
                
                <div class="form-group">
                    <label>Ghi chú:</label>
                    <?=form_input(
                        $field_name = 'memo', 
                        set_value($field_name, null, false),
                        array(
                            'class' => 'form-control autocomplete',
                        )
                    )?>
                </div>
                <button type="button" onClick="Cashbook.submitbutton(this, 'submit')" class="btn btn-primary">Nhập</button>
                <?php if ($this->uri->segment(2) == 'add'): ?>
                    <button type="button" onClick="Cashbook.submitbutton(this, 'continue')" class="btn btn-primary">Nhập & Tiếp tục</button>
                <?php endif ?>
                <?php if ($this->uri->segment(2) == 'edit'): ?>
                    <button type="button" onClick="Cashbook.submitbutton(this, 'delete')" class="btn btn-danger pull-right">Xóa</button>
                <?php endif ?>
            </div>
        </div>
    </form>
</div>