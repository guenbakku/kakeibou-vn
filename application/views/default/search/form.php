<script type="text/javascript">
    function toggle_collapse_form(){
        $('.advance-search').collapse('toggle');
    }
</script>

<div class="container">
    <?php echo form_open($form_url, array('method' => 'get', 'class' => 'form-vertical'))?>
        <div class="panel panel-default">
            <div class="panel-heading"><strong><?=$title?></strong></div>
            <div class="panel-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-xs-12">
                            <label>Memo hoặc Số tiền:</label>
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
                        <label>
                            <input type="hidden" value="0" name="<?=$field_name = 'hide_pair_inout'?>">
                            <?=form_checkbox(
                                array(
                                    'name'      => $field_name,
                                    'value'     => '1',
                                    'checked'   => (bool)set_value($field_name, TRUE),
                                )
                            )?>
                            Ẩn dữ liệu lưu động nội bộ
                        </label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary">Tìm</button>
                <button type="button" class="btn btn-default" onclick="toggle_collapse_form()">Tìm chi tiết</button>
            </div>
        </div>
    </form>
</div>