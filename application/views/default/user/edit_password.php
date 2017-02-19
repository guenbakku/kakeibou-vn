<?=$this->template->get_view('elements/page-nav')?>

<div class="container">
    <div class="panel panel-default">
        <div class="panel-body">
            <?php echo form_open(base_url('setting/user/edit/password'), array('class' => 'form-vertical'))?>
                <div class="form-group">
                    <label for="old_password">Mật khẩu hiện tại:</label>
                    <?=form_input(
                        array(
                            'name' => $field_name = 'old_password',
                            'id'   => $field_name,
                            'type' => 'password',
                        ),
                        set_value($field_name, null),
                        array(
                            'class' => 'form-control'
                        )
                    )?>
                </div>
                <div class="form-group">
                    <label for="new_password">Mật khẩu mới:</label>
                    <?=form_input(
                        array(
                            'name' => $field_name = 'new_password',
                            'id'   => $field_name,
                            'type' => 'password',
                        ),
                        set_value($field_name, null),
                        array(
                            'class' => 'form-control'
                        )
                    )?>
                </div>
                <div class="form-group">
                    <label for="new_password_confirm">Mật khẩu mới (xác nhận):</label>
                    <?=form_input(
                        array(
                            'name' => $field_name = 'new_password_confirm',
                            'id'   => $field_name,
                            'type' => 'password',
                        ),
                        set_value($field_name, null),
                        array(
                            'class' => 'form-control'
                        )
                    )?>
                </div>
                <input type="submit" class="btn btn-primary" value="<?=Consts::LABEL['submit']?>">
            </form>
        </div>
    </div>
</div>

