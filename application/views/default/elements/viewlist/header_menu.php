<?php
    $menu = [
        'viewlist_summary' => [
            'url' => $dateFormatType !== 'ymd'? base_url(['viewlist', 'summary', $date]) : '',
            'label' => 'Danh sách tóm tắt',
            'icon' => 'fa fa-th-list',
        ],
        'viewlist_detail' => [
            'url' => $dateFormatType !== null? base_url(['viewlist', 'detail', $date]) : '',
            'label' => 'Danh sách chi tiết',
            'icon' => 'fa fa-list',
        ],
        'viewchart_line' => [
            'url' => $dateFormatType !== 'ymd'? base_url(['viewlist', 'chart', 'line', $date]) : '',
            'label' => 'Biểu đồ đường',
            'icon' => 'fa fa-line-chart',
        ],
        'viewchart_pie' => [
            'url' => $dateFormatType !== null? base_url(['viewlist', 'chart', 'pie', $date]) : '',
            'label' => 'Biểu đồ quạt',
            'icon' => 'fa fa-pie-chart',
        ],
    ]
?>

<div class="dropdown pull-left">
    <a class="dropdown-toggle btn btn-default btn-sm" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">
        <span class="glyphicon glyphicon-menu-hamburger">
    </a>
    <ul class="dropdown-menu navigation">
        <?php foreach($menu as $i => $item):?>
        <?php if (!empty($item['url'])): ?>
            <li href="<?=$item['url']?>">
                <a href="<?=$item['url']?>">
                    <span class="<?=$item['icon']?>" aria-hidden="true"></span>&nbsp;
                    <?=$item['label']?>
                </a>
            </li>
        <?php endif ?>
        <?php endforeach ?>
    </ul>
</div>
