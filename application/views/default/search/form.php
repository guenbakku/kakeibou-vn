<script type="text/javascript">
    function toggle_collapse_form(){
        $('.search-form').collapse('toggle');
    }
</script>

<div class="container">
    <?php echo form_open($form_url, array('method' => 'get', 'class' => 'form-vertical'))?>
        <div class="panel panel-default">
            <div class="panel-heading" onclick="toggle_collapse_form()"><strong><?=$title?></strong><span class="glyphicon glyphicon-menu-down pull-right"></span></div>
            <div class="panel-body search-form <?=$show_form? 'collapse in':'collapse'?>">
                <div class="form-group">
                    <div class="row">
                        <div class="col-xs-6">
                            <label>Số tiền:</label>
                            <?=form_input(
                                array(
                                    'name' => $field_name = 'amount',
                                    'type' => 'number',
                                    'pattern' => '\d*',
                                ),
                                set_value($field_name, null),
                                array(
                                    'class' => 'form-control autofocus',
                                )
                            )?>
                        </div>
                        <div class="col-xs-6">
                            <label>Loại:</label>
                            <?=form_dropdown(
                                $field_name = 'inout_type', 
                                $select['inout_types'], 
                                set_value($field_name, 0), 
                                array(
                                    'class' => 'form-control',
                                )
                            )?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>Memo:</label>
                    <?=form_input(
                        array(
                            'name' => $field_name = 'memo',
                            'type' => 'text',
                        ),
                        set_value($field_name, null, false),
                        array(
                            'class' => 'form-control',
                        )
                    )?>
                </div>
                <div class="form-group">
                    <label>Thời gian:</label>
                    <div class="input-group" style="width:100%">
                        <span class="input-group-addon" style="min-width:60px">Từ</span>
                        <?=form_input(
                            array(
                                'name' => $field_name = 'from',
                                'type' => 'date',
                            ),
                            set_value($field_name, null),
                            array(
                                'class' => 'form-control',
                            )
                        )?>
                    </div>
                    <div class="input-group" style="width:100%">
                        <span class="input-group-addon" style="min-width:60px">Đến</span>
                        <?=form_input(
                            array(
                                'name' => $field_name = 'to',
                                'type' => 'date',
                            ),
                            set_value($field_name, null),
                            array(
                                'class' => 'form-control',
                            )
                        )?>
                    </div>
                </div>
                <div class="form-group">
                    <label>Phụ trách</label>
                    <?=form_dropdown(
                        $field_name = 'player', 
                        $select['players'], 
                        set_value($field_name, 0), 
                        array(
                            'class' => 'form-control',
                        )
                    )?>
                </div>
                <button type="submit" class="btn btn-primary">Tìm</button>
            </div>
        </div>
    </form>
</div>