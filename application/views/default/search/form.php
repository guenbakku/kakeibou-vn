<script type="text/javascript">
    function toggle_collapse_form() {
        $('.advance-search').collapse('toggle');
    }

    $(function() {
        $('.clear-date').click(function(evt) {
            $(this).parents('.row').find('input[type=date]').val('');
        });
    })
</script>

<div class="container">
    <?php echo form_open($url['form'], array('method' => 'get', 'class' => 'form-vertical'))?>
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-xs-12">
                            <label>Ghi chú hoặc Số tiền:</label>
                            <?=form_input(
                                array(
                                    'name' => $field_name = 'memo_or_amount',
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
                        <input type="hidden" value="0" name="<?=$field_name = 'only_show_temp_inout'?>">
                        <?=form_checkbox(
                            array(
                                'name'      => $field_name,
                                'value'     => '1',
                                'checked'   => (bool)set_value($field_name, false),
                            )
                        )?>
                        Chỉ hiện dữ liệu Danh nghĩa
                    </label>
                </div>
                <div class="form-group">
                    <label>
                        <input type="hidden" value="0" name="<?=$field_name = 'also_show_pair_inout'?>">
                        <?=form_checkbox(
                            array(
                                'name'      => $field_name,
                                'value'     => '1',
                                'checked'   => (bool)set_value($field_name, false),
                            )
                        )?>
                        Hiện dữ liệu lưu động nội bộ
                    </label>
                </div>
                <div class="advance-search collapse">
                    <div class="form-group">
                        <div class="row">
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
                            <div class="col-xs-6">
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
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Thời gian thu chi:</label>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="input-group">
                                    <span class="input-group-addon" style="min-width:60px">Từ</span>
                                    <?=form_input(
                                        array(
                                            'name' => $field_name = 'inout_from',
                                            'type' => 'date',
                                        ),
                                        set_value($field_name, null),
                                        array(
                                            'class' => 'form-control',
                                        )
                                    )?>
                                    <span class="input-group-addon" style="min-width:50px">
                                        <span class="glyphicon glyphicon-remove clear-date"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="input-group">
                                    <span class="input-group-addon" style="min-width:60px">Đến</span>
                                    <?=form_input(
                                        array(
                                            'name' => $field_name = 'inout_to',
                                            'type' => 'date',
                                        ),
                                        set_value($field_name, null),
                                        array(
                                            'class' => 'form-control',
                                        )
                                    )?>
                                    <span class="input-group-addon" style="min-width:50px">
                                        <span class="glyphicon glyphicon-remove clear-date"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Thời gian nhập/chỉnh sửa:</label>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="input-group">
                                    <span class="input-group-addon" style="min-width:60px">Từ</span>
                                    <?=form_input(
                                        array(
                                            'name' => $field_name = 'modified_from',
                                            'type' => 'date',
                                        ),
                                        set_value($field_name, null),
                                        array(
                                            'class' => 'form-control',
                                        )
                                    )?>
                                    <span class="input-group-addon" style="min-width:50px">
                                        <span class="glyphicon glyphicon-remove clear-date"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="input-group">
                                    <span class="input-group-addon" style="min-width:60px">Đến</span>
                                    <?=form_input(
                                        array(
                                            'name' => $field_name = 'modified_to',
                                            'type' => 'date',
                                        ),
                                        set_value($field_name, null),
                                        array(
                                            'class' => 'form-control',
                                        )
                                    )?>
                                    <span class="input-group-addon" style="min-width:50px">
                                        <span class="glyphicon glyphicon-remove clear-date"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary"><?=Consts::LABEL['search']?></button>
                <a href="" onclick="toggle_collapse_form(); return false">Tìm chi tiết</a>
            </div>
        </div>
    </form>
</div>
