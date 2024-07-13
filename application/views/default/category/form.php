<?php
$disabled_attr = $this->router->fetch_method() == 'edit'
                 ? ['disabled' => true]
                 : [];
?>

<?= $this->template->get_view('elements/page-nav'); ?>
<div class="container">
    <?= form_open($url['form'], ['class' => 'form-vertical']); ?>
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="form-group">
                    <label>Loại:</label>
                    <?= form_dropdown(
                        $field_name = 'inout_type_id',
                        $select['inout_types'],
                        set_value($field_name, 1),
                        [
                            'class' => 'form-control',
                        ] + $disabled_attr
                    ); ?>
                </div>
                <div class="form-group">
                    <label>Tên danh mục:</label>
                    <?= form_input(
                        [
                            'name' => $field_name = 'name',
                            'type' => 'text',
                        ],
                        set_value($field_name, null, false),
                        [
                            'class' => 'form-control',
                        ]
                    ); ?>
                </div>
                <div class="form-group">
                    <label>
                        <input type="hidden" value="0" name="<?= $field_name = 'is_month_fixed_money'; ?>">
                        <?= form_checkbox(
                            [
                                'name' => $field_name,
                                'value' => '1',
                                'checked' => (bool) set_value($field_name, false),
                            ]
                        ); ?>
                        Khoản thu chi cố định hàng tháng
                    </label>
                </div>

                <button type="submit" onClick="Cashbook.submitButton(this, 'submit')" class="btn btn-primary"><?= Consts::LABEL['submit']; ?></button>
                <?php if ($this->router->fetch_method() == 'edit') { ?>
                    <button type="button" onClick="Cashbook.submitButton(this, 'delete')" class="btn btn-danger pull-right"><?= Consts::LABEL['delete']; ?></button>
                <?php } ?>

            </div>
        </div>
    </form>
</div>
