<?= $this->template->get_view('elements/page-nav'); ?>

<div class="container">
    <div class="panel panel-default">
        <div class="panel-body">
            <?= form_open(base_url('setting/user/edit/password'), ['class' => 'form-vertical']); ?>
                <div class="form-group">
                    <label for="old_password">Mật khẩu hiện tại:</label>
                    <?= form_input(
                        [
                            'name' => $field_name = 'old_password',
                            'id' => $field_name,
                            'type' => 'password',
                        ],
                        set_value($field_name, null),
                        [
                            'class' => 'form-control',
                        ]
                    ); ?>
                </div>
                <div class="form-group">
                    <label for="new_password">Mật khẩu mới:</label>
                    <?= form_input(
                        [
                            'name' => $field_name = 'new_password',
                            'id' => $field_name,
                            'type' => 'password',
                        ],
                        set_value($field_name, null),
                        [
                            'class' => 'form-control',
                        ]
                    ); ?>
                </div>
                <div class="form-group">
                    <label for="new_password_confirm">Mật khẩu mới (xác nhận):</label>
                    <?= form_input(
                        [
                            'name' => $field_name = 'new_password_confirm',
                            'id' => $field_name,
                            'type' => 'password',
                        ],
                        set_value($field_name, null),
                        [
                            'class' => 'form-control',
                        ]
                    ); ?>
                </div>
                <button type="submit" class="btn btn-primary" onClick="Cashbook.submitButton(this, 'submit')"><?= Consts::LABEL['submit']; ?></button>
            </form>
        </div>
    </div>
</div>

