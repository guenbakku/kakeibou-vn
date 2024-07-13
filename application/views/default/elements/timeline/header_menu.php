<?php
$menu = [
    'timeline_summary' => [
        'url' => 'ymd' !== $dateFormatType ? base_url(['timeline', 'summary', $date]) : '',
        'label' => 'Danh sách tóm tắt',
        'icon' => 'fa fa-th-list',
    ],
    'timeline_detail' => [
        'url' => null !== $dateFormatType ? base_url(['timeline', 'detail', $date]) : '',
        'label' => 'Danh sách chi tiết',
        'icon' => 'fa fa-list',
    ],
    'viewchart_line' => [
        'url' => 'ymd' !== $dateFormatType ? base_url(['timeline', 'chart', 'line', $date]) : '',
        'label' => 'Biểu đồ đường',
        'icon' => 'fa fa-line-chart',
    ],
    'viewchart_pie' => [
        'url' => null !== $dateFormatType ? base_url(['timeline', 'chart', 'pie', $date]) : '',
        'label' => 'Biểu đồ quạt',
        'icon' => 'fa fa-pie-chart',
    ],
];
?>

<div class="dropdown pull-left">
    <a class="dropdown-toggle btn btn-default btn-sm" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true">
        <span class="glyphicon glyphicon-menu-hamburger">
    </a>
    <ul class="dropdown-menu navigation">
        <?php foreach ($menu as $i => $item) { ?>
        <?php if (!empty($item['url'])) { ?>
            <li href="<?= $item['url']; ?>">
                <a href="<?= $item['url']; ?>">
                    <span class="<?= $item['icon']; ?>" aria-hidden="true"></span>&nbsp;
                    <?= $item['label']; ?>
                </a>
            </li>
        <?php } ?>
        <?php } ?>
    </ul>
</div>
