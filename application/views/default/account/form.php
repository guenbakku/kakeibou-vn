<?= $this->template->get_view('elements/page-nav'); ?>
<div class="container">
    <?= form_open($url['form'], ['class' => 'form-vertical']); ?>
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-xs-12">
                            <label>Tên tài khoản:</label>
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
                    </div>
                </div>
                <div class="form-group">
                    <div class="row">
                        <div class="col-xs-12">
                            <label>Ghi chú:</label>
                            <?= form_input(
                                [
                                    'name' => $field_name = 'description',
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
                <button type="submit" onClick="Cashbook.submitButton(this, 'submit')" class="btn btn-primary"><?= Consts::LABEL['submit']; ?></button>
                <?php if ($this->router->fetch_method() == 'edit') { ?>
                    <button type="button" onClick="Cashbook.submitButton(this, 'delete')" class="btn btn-danger pull-right"><?= Consts::LABEL['delete']; ?></button>
                <?php } ?>

            </div>
        </div>
    </form>
</div>
