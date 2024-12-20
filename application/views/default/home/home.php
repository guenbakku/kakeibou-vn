<?php
$today = date('Y-m-d');
?>
<script type="text/javascript">
    $(function () {
        /*
         * Hiện popover cho phần "Số tiền có thể chi"
         */
        $('[data-toggle="popover"]').popover({
            'html': true,
            'container': 'body',
            'placement': 'bottom',
            'title': 'Chi tiết',
            'content': function(){
                var outgo_status = $(this).parent().find('div.outgo-status');
                return outgo_status.html();
            },
        })

        /*
         * Ẩn tất cả popovers đang mở khi click vào chỗ khác trên màn hình
         */
        $('body').on('click', function (e) {
            $('[data-toggle="popover"]').each(function () {
                if (!$(this).is(e.target)
                    && $(this).is('[aria-describedby]')
                    && $(this).has(e.target).length === 0
                    && $('.popover').has(e.target).length === 0) {
                    $(this).popover('hide');
                }
            });
        });
    })

</script>

<div class="container">
    <?php // Chỉ hiện bảng Chi thực tế/Dự định nếu có setting "Dự định chi tháng này"?>
    <?php if ($liquidOutgoStatus['month']['estimated'] > 0) { ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <a class="pull-right" href="<?= $url['summaryThisMonth']; ?>">
                <span class="glyphicon glyphicon-option-horizontal"></span>
            </a>
            <strong>Số tiền có thể chi</strong><br>
            <span class="small text-muted"><em>(Không tính các khoản chi cố định)</em></span>
        </div>
        <table class="table table-bordered">
            <tr>
                <th style="width:33.3%">
                    Hôm nay
                </th>
                <td class="text-center">
                    <?= $this->template->get_view('home/home_estimated_outgo_detail', $liquidOutgoStatus['today']); ?>
                </td>
            </tr>
            <tr>
                <th>
                    Tháng này
                </th>
                <td class="text-center">
                    <?= $this->template->get_view('home/home_estimated_outgo_detail', $liquidOutgoStatus['month']); ?>
                </td>
            </tr>
        </table>
    </div>
    <?php } ?>

    <?= $this->template->get_view('home/home_month_sum', ['month_sum' => $month_sum]); ?>
    <?= $this->template->get_view('home/home_remaining', ['remaining' => $remaining]); ?>
</div>
