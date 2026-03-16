<?= $this->template->get_view('elements/page-nav'); ?>
<div class="container">
    <?= form_open($url['form'], ['class' => 'form-vertical']); ?>
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-xs-12">
                            <p>Bạn có chắc chắn muốn xóa tài khoản dưới đây?</p>
                            <div class="well well-sm" style="margin-bottom: 0">
                                <div><?= $account['name']; ?></div>
                                <div class="small text-muted"><?= $account['description']; ?></div>
                            </div>
                        </div>
                    </div>
                    <?php if (!$is_account_empty): ?>
                        <div class="row" style="margin-top: 20px">
                            <div class="col-xs-12">
                                <p>Tài khoản đang chứa dữ liệu ghi chép. <br>
                                Nếu bạn muốn xóa tài khoản này, cần phải di chuyển dữ liệu ghi chép sang tài khoản khác.</p>
                                <label>Tài khoản khác:</label>
                                <?= form_dropdown(
                                    $field_name = 'target_account_id',
                                    $select['target_accounts'],
                                    null,
                                    [
                                        'class' => 'form-control',
                                    ]
                                ); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-danger"><?= settings('label.delete'); ?></button>
            </div>
        </div>
    </form>
</div>
