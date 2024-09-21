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
    <?= form_open($url['form'], ['method' => 'get', 'class' => 'form-vertical']); ?>
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-xs-12">
                            <label>Ghi chú hoặc Số tiền:</label>
                            <?= form_input(
                                [
                                    'name' => $field_name = 'memo_or_amount',
                                    'type' => 'text',
                                ],
                                set_value($field_name, null, false),
                                [
                                    'class' => 'form-control',
                                ]
                            ); ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label>
                        <input type="hidden" value="0" name="<?= $field_name = 'only_show_temp_inout'; ?>">
                        <?= form_checkbox(
                            [
                                'name' => $field_name,
                                'value' => '1',
                                'checked' => (bool) set_value($field_name, false),
                            ]
                        ); ?>
                        Chỉ hiện dữ liệu Danh nghĩa
                    </label>
                </div>
                <div class="form-group">
                    <label>
                        <input type="hidden" value="0" name="<?= $field_name = 'also_show_pair_inout'; ?>">
                        <?= form_checkbox(
                            [
                                'name' => $field_name,
                                'value' => '1',
                                'checked' => (bool) set_value($field_name, false),
                            ]
                        ); ?>
                        Hiện dữ liệu lưu động nội bộ
                    </label>
                </div>
                <div class="advance-search collapse">
                    <div class="form-group">
                        <div class="row">
                            <div class="col-xs-6" style="padding-right: 7.5px">
                                <label>Loại:</label>
                                <?= form_dropdown(
                                    $field_name = 'inout_type',
                                    $select['inout_types'],
                                    set_value($field_name, 0),
                                    [
                                        'class' => 'form-control',
                                    ]
                                ); ?>
                            </div>
                            <div class="col-xs-6" style="padding-left: 7.5px">
                                <label>Danh mục</label>
                                <?= form_dropdown(
                                    $field_name = 'category',
                                    $select['initial_categories'],
                                    set_value($field_name, 0),
                                    [
                                        'class' => 'form-control',
                                    ]
                                ); ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row">
                            <div class="col-xs-12">
                                <label>Phụ trách</label>
                                <?= form_dropdown(
                                    $field_name = 'player',
                                    $select['players'],
                                    set_value($field_name, 0),
                                    [
                                        'class' => 'form-control',
                                    ]
                                ); ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Thời gian thu chi:</label>
                        <div class="row">
                            <div class="col-xs-6" style="padding-right: 7.5px">
                                <label>Từ ngày</label>
                                <?= form_input(
                                    [
                                        'name' => $field_name = 'inout_from',
                                        'type' => 'date',
                                    ],
                                    set_value($field_name, null),
                                    [
                                        'class' => 'form-control',
                                    ]
                                ); ?>
                            </div>
                            <div class="col-xs-6" style="padding-left: 7.5px">
                                <label>Đến ngày</label>
                                <?= form_input(
                                    [
                                        'name' => $field_name = 'inout_to',
                                        'type' => 'date',
                                    ],
                                    set_value($field_name, null),
                                    [
                                        'class' => 'form-control',
                                    ]
                                ); ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Thời gian nhập/chỉnh sửa:</label>
                        <div class="row">
                            <div class="col-xs-6" style="padding-right: 7.5px">
                                <label>Từ ngày</label>
                                <?= form_input(
                                    [
                                        'name' => $field_name = 'modified_from',
                                        'type' => 'date',
                                    ],
                                    set_value($field_name, null),
                                    [
                                        'class' => 'form-control',
                                    ]
                                ); ?>
                            </div>
                            <div class="col-xs-6" style="padding-left: 7.5px">
                                <label>Đến ngày</label>
                                <?= form_input(
                                    [
                                        'name' => $field_name = 'modified_to',
                                        'type' => 'date',
                                    ],
                                    set_value($field_name, null),
                                    [
                                        'class' => 'form-control',
                                    ]
                                ); ?>
                            </div>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" onClick="Cashbook.submitButton(this, 'submit')"><?= settings('label.search'); ?></button>
                <a href="" onclick="toggle_collapse_form(); return false">Tìm chi tiết</a>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript">
const INOUT_TYPES = <?= json_encode($inout_types); ?>;

<?php
// Category select list will be converted to json object and passed to javascript.
// So it must be convert to the number-index array (array_to_list) to reserve the order of items
// before converting it to json.
    ?>
const CATEGORIES = {
    [INOUT_TYPES.income]: <?= json_encode(array_to_list($select['income_categories'])); ?>,
    [INOUT_TYPES.outgo]: <?= json_encode(array_to_list($select['outgo_categories'])); ?>,
}

const reRenderCategorySelectItems = (inoutTypeId, previousInoutTypeId) => {
    if (inoutTypeId !== previousInoutTypeId) {
        // Generate select options based on inoutTypeId
        const optionElms = CATEGORIES[inoutTypeId].map((item) => {
            const elm = $('<option>', {
                'value': item[0],
                'text': item[1],
            });
            return elm;
        });

        // Re-render category select list
        $('[name=category]').html('');
        optionElms.forEach((elm) => {
            $('[name=category]').append(elm);
        });
    }

    return true;
};

$('[name=inout_type]').switcher({
    targets: ['[name=category]'],
    disableValues: ['0'],
    onBeforeEnable: reRenderCategorySelectItems,
});
</script>
