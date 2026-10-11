<div class="container">
    <div class="panel panel-default home-shortcuts">
        <div class="panel-heading home-shortcuts-heading" role="button" tabindex="0"
            data-toggle="collapse" data-target="#home-shortcuts-body"
            aria-controls="home-shortcuts-body" aria-expanded="true">
            <strong>Truy cập nhanh</strong>
            <span class="glyphicon glyphicon-chevron-down pull-right home-shortcuts-toggle" aria-hidden="true"></span>
        </div>
        <div class="panel-body collapse in" id="home-shortcuts-body">
            <div class="row" style="margin-right:-7.5px; margin-left:-7.5px">
                <div class="col-xs-4" style="padding-left:7.5px; padding-right:7.5px;">
                    <a class="btn btn-default btn-block home-shortcut" href="<?= $url['detailThisMonth']; ?>">
                        <span class="fa fa-list-ul" aria-hidden="true"></span>
                        <span>Chi tiết tháng này</span>
                    </a>
                </div>
                <div class="col-xs-4" style="padding-left:7.5px; padding-right:7.5px;">
                    <a class="btn btn-default btn-block home-shortcut" href="<?= $url['summaryThisMonth']; ?>">
                        <span class="fa fa-calendar" aria-hidden="true"></span>
                        <span>Tổng hợp tháng này</span>
                    </a>
                </div>
                <div class="col-xs-4" style="padding-left:7.5px; padding-right:7.5px;">
                    <a class="btn btn-default btn-block home-shortcut" href="<?= $url['summaryThisYear']; ?>">
                        <span class="fa fa-calendar-o" aria-hidden="true"></span>
                        <span>Tổng hợp năm nay</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php // Chỉ hiện bảng Chi thực tế/Dự định nếu có setting "Dự định chi tháng này"?>
    <?php if ($liquidOutgoStatus['month']['estimated'] > 0) { ?>
    <div class="panel panel-default">
        <div class="panel-heading">
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
    });

    $(function () {
        // Đóng mở panel Truy cập nhanh
        var shortcutHeading = $('.home-shortcuts-heading');
        var shortcutBody = $('#home-shortcuts-body');
        var shortcutStorageKey = 'home.shortcuts.collapsed';
        var isShortcutCollapsed = false;

        try {
            isShortcutCollapsed = window.localStorage.getItem(shortcutStorageKey) === 'true';
        } catch (error) {
            if (window.console && window.console.error) {
                window.console.error('Unable to read the home shortcuts state from local storage.', error);
            }
        }

        if (isShortcutCollapsed) {
            shortcutBody.removeClass('in');
            shortcutHeading.attr('aria-expanded', 'false');
            shortcutHeading.find('.home-shortcuts-toggle')
                .removeClass('glyphicon-chevron-down')
                .addClass('glyphicon-chevron-right');
        }

        shortcutHeading.on('keydown', function (e) {
            if (e.which === 13 || e.which === 32) {
                e.preventDefault();
                $(this).trigger('click');
            }
        });

        shortcutBody.on('show.bs.collapse hide.bs.collapse', function (e) {
            var isExpanded = e.type === 'show';
            shortcutHeading.attr('aria-expanded', isExpanded ? 'true' : 'false');
            shortcutHeading.find('.home-shortcuts-toggle')
                .toggleClass('glyphicon-chevron-down', isExpanded)
                .toggleClass('glyphicon-chevron-right', !isExpanded);

            try {
                window.localStorage.setItem(shortcutStorageKey, isExpanded ? 'false' : 'true');
            } catch (error) {
                if (window.console && window.console.error) {
                    window.console.error('Unable to save the home shortcuts state to local storage.', error);
                }
            }
        });
    });
</script>
