<?= $this->template->get_view('elements/page-nav'); ?>
<div class="container">
    <?= form_open($url['form'], ['class' => 'form-vertical']); ?>
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="form-group">
                    <div class="row">
                        <div class="col-xs-12">
                            <p>Bạn có chắc chắn muốn xóa danh mục dưới đây?</p>
                            <div class="well well-sm" style="margin-bottom: 0px">
                                <label class="text-muted">Tên danh mục:</label>
                                <div><?= $category['name']; ?></div>
                            </div>
                            <?php if ($inout_type['Chi'] == $category['inout_type_id']): ?>
                                <div class="well well-sm" style="margin-bottom: 0; margin-top: 10px">
                                    <label class="text-muted">Dự định chi hàng tháng:</label>
                                    <div><?= number_format($category['month_estimated_amount']); ?></div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php if (!$is_category_empty): ?>
                        <hr>
                        <div class="row" style="margin-top: 20px">
                            <div class="col-xs-12">
                                <p>Danh mục đang chứa dữ liệu ghi chép. <br>
                                Nếu bạn muốn xóa danh mục này, cần phải di chuyển dữ liệu ghi chép sang danh mục khác.</p>
                                <label>Danh mục khác:</label>
                                <?= form_dropdown(
                                    $field_name = 'target_category_id',
                                    $select['target_categories'],
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
