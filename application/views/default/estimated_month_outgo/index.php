<?= $this->template->get_view('elements/page-nav'); ?>
<div class="container">
    <div class="panel panel-default">
        <table class="table table-bordered" style="border-bottom:1px solid; border-color:inherit">
            <tr>
                <td>
                    <a class="btn btn-default pull-right" href="<?= $url['edit']; ?>"><?= settings('label.edit'); ?></a>
                </td>
            </tr>
        </table>

        <?php if (count($categories) > 0): ?>
            <table class="table table-bordered table-ex">
                <?php foreach ($categories as $category): ?>
                    <tr>
                        <td>
                            <div class="row">
                                <div class="col-xs-7">
                                    <?= $category['name']; ?>
                                    <?php if ($category['is_month_fixed_money'] == 1): ?>
                                        <span class="label label-default">Cố định</span>
                                    <?php endif; ?>
                                </div>
                                <div class="col-xs-5 text-right">
                                    <?= number_format($category['month_estimated_amount']); ?>
                                </div>
                            </div>
                            <div class="row" style="margin-top:2px">
                                <div class="col-xs-12 small text-muted text-right">
                                    Đã chi: <?= number_format($category['actual_amount']); ?>
                                </div>
                            </div>
                            <div class="progress" style="margin-bottom:0; margin-top:4px; height:8px">
                                <div class="progress-bar <?= $category['percent'] <= 20 ? 'progress-bar-danger' : 'progress-bar-info'; ?>"
                                    role="progressbar"
                                    aria-valuenow="<?= $category['percent']; ?>"
                                    aria-valuemin="0"
                                    aria-valuemax="100"
                                    style="width:<?= $category['percent']; ?>%;">
                                </div>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php else: ?>
            <p class="text-center" style="margin-top:10px">Chưa có dữ liệu</p>
        <?php endif; ?>
    </div>
</div>
