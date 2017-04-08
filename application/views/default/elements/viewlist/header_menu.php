<?php
    $menu = [
        'viewlist_summary' => [
            'url' => base_url(['viewlist', 'summary', $date]),
            'label' => 'Danh sách tóm tắt',
            'disabled' => $dateFormatType === 'ymd'? 'disabled' : '',
            'icon' => 'fa fa-th-list',
        ],
        'viewlist_detail' => [
            'url' => base_url(['viewlist', 'detail', $date]),
            'label' => 'Danh sách chi tiết',
            'disabled' => $dateFormatType === null? 'disabled' : '',
            'icon' => 'fa fa-list',
        ],
        'viewchart_line' => [
            'url' => base_url(['viewlist', 'chart', 'line', $date]),
            'label' => 'Biểu đồ đường',
            'disabled' => $dateFormatType === 'ymd'? 'disabled' : '',
            'icon' => 'fa fa-line-chart',
        ],
        'viewchart_pie' => [
            'url' => base_url(['viewlist', 'chart', 'pie', $date]),
            'label' => 'Biểu đồ quạt',
            'disabled' => $dateFormatType === null? 'disabled' : '',
            'icon' => 'fa fa-pie-chart',
        ],
    ]
?>

<div class="dropdown pull-left">
    <a class="dropdown-toggle btn btn-default btn-sm" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">
        <span class="glyphicon glyphicon-menu-hamburger">
    </a>
    <ul class="dropdown-menu navigation">
        <?php foreach($menu as $i => $item): ?>
        <li href="<?=$item['url']?>">
            <a class="<?=$item['disabled']?>" href="<?=$item['url']?>">
                <span class="<?=$item['icon']?>" aria-hidden="true"></span>&nbsp;
                <?=$item['label']?>
            </a>
        </li>
        <?php endforeach ?>
    </ul>
</div>
