<?php 
    $url['viewlist_summary'] = base_url(['viewlist', 'summary', $date]);
    $url['viewlist_detail'] = base_url(['viewlist', 'detail', $date]);
    $url['viewchart_line'] = base_url(['viewlist', 'chart', 'line', $date]);
    $url['viewchart_pie'] = base_url(['viewlist', 'chart', 'pie', $date]);
    
    $disabled = [
        'viewlist_summary' => '',
        'viewlist_detail' => '',
        'viewchart_line' => '',
        'viewchart_pie' => '',
    ];
    if ($dateFormatType === null) {
        $disabled['viewlist_detail'] = 'disabled';
        $disabled['viewchart_pie'] = 'disabled';
    }
    if ($dateFormatType === 'ymd') {
        $disabled['viewlist_summary'] = 'disabled';
        $disabled['viewchart_line'] = 'disabled';
    }
?>

<div class="container">
    <div class="form-group">
        <div class="btn-group btn-group-justified navigation">
            <a class="btn btn-default btn-sm <?=$disabled['viewlist_summary']?>" href="<?=$url['viewlist_summary']?>">
                <span class="fa fa-th-list" aria-hidden="true"></span>
            </a>
            <a class="btn btn-default btn-sm <?=$disabled['viewlist_detail']?>" href="<?=$url['viewlist_detail']?>">
                <span class="fa fa-list" aria-hidden="true"></span>
            </a>
            <a class="btn btn-default btn-sm <?=$disabled['viewchart_line']?>" href="<?=$url['viewchart_line']?>">
                <span class="fa fa-line-chart" aria-hidden="true"></span>
            </a>
            <a class="btn btn-default btn-sm <?=$disabled['viewchart_pie']?>" href="<?=$url['viewchart_pie']?>">
                <span class="fa fa-pie-chart" aria-hidden="true"></span>
            </a>
        </div>
    </div>
</div>