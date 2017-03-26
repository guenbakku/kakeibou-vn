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
         * hide any open popovers when the anywhere else in the body is clicked
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
    <?php // Chỉ hiện bảng Chi thực tế/Dự định nếu có setting "Dự định chi tháng này" ?>
    <?php if ($liquidOutgoStatus['month']['estimated'] > 0): ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <strong>Số tiền có thể chi</strong><br>
            <span class="small text-muted"><em>(Không tính các khoản chi cố định)</em></span>
        </div>
        <table class="table table-bordered">
            <tr>
                <th style="width:33.3%">
                    <a href="<?=$url['detailToday']?>">Hôm nay</a>
                </th>
                <td class="text-center">
                    <div class="sr-only outgo-status">
                        <?=$this->template->get_view('home/estimated_outgo_detail', $liquidOutgoStatus['today'])?>
                    </div>
                    <div class="progress" style="margin-bottom:0" data-toggle="popover">
                        <div class="progress-bar progress-bar-info" role="progressbar"
                            aria-valuenow="<?=$liquidOutgoStatus['today']['remain_percent']?>" 
                            aria-valuemin="0" aria-valuemax="100" 
                            style="width: <?=$liquidOutgoStatus['today']['remain_percent']?>%">
                            <span><?=currency($liquidOutgoStatus['today']['remain'], false)?></span>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th>
                    <a href="<?=$url['summaryThisMonth']?>">Tháng này</a>
                </th>
                <td class="text-center">
                    <div class="sr-only outgo-status">
                        <?=$this->template->get_view('home/estimated_outgo_detail', $liquidOutgoStatus['month'])?>
                    </div>
                    <div class="progress" style="margin-bottom:0" data-toggle="popover" >
                        <div class="progress-bar progress-bar-info" role="progressbar"
                        aria-valuenow="<?=$liquidOutgoStatus['month']['remain_percent']?>" 
                        aria-valuemin="0" aria-valuemax="100" 
                        style="width: <?=$liquidOutgoStatus['month']['remain_percent']?>%">
                            <span><?=currency($liquidOutgoStatus['month']['remain'], false)?></span>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    <?php endif ?>

    <div class="panel panel-default">
        <div class="panel-heading">
            <strong>Thu chi tháng này</strong>
        </div>
        <table class="table table-bordered">
            <tr>
                <th style="width:33.3%" class="text-center">Thu</th>
                <th style="width:33.3%" class="text-center">Chi</th>
                <th style="width:33.3%" class="text-center">Chênh lệch</th>
            </tr>
            <tr>
                <td class="text-right"><?=currency($month_sum['thu'])?></td>
                <td class="text-right"><?=currency($month_sum['chi'])?></td>
                <td class="text-right"><?=currency($month_sum['tong'])?></td>
            </tr>
        </table>
    </div>
    
    <div class="panel panel-default">
        <div class="panel-heading"><strong>Tiền còn lại</strong></div>
        <table class="table table-bordered">
            <tr>
                <th style="width:34%"><br></th>
                <th class="text-center">Hiện tại</th>
                <th class="text-center" style="width:33.3%">Tương lai</th>
            </tr>
            <?php foreach ($remaining as $k => $v): ?>
            <tr>
                <th><?=$k?></th>
                <td class="text-right"><?=currency($v[0])?></td>
                <td class="text-right"><?=currency($v[1])?></td>
            </tr>
            <?php endforeach ?>
        </table>
    </div>
</div>