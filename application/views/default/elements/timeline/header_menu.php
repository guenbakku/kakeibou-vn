<?php
$menu = [
    [
        'url' => $dateFormatType !== 'ymd' ? base_url(['timeline', 'summary', $date]) : '',
        'label' => 'Danh sách tóm tắt',
        'icon' => 'fa fa-th-list',
    ],
    [
        'url' => $dateFormatType !== null ? base_url(['timeline', 'detail', $date]) : '',
        'label' => 'Danh sách chi tiết',
        'icon' => 'fa fa-list',
    ],
    [
        'url' => $dateFormatType !== 'ymd' ? base_url(['timeline', 'chart', 'inout', $date]) : '',
        'label' => 'Biểu đồ thu chi',
        'icon' => 'fa fa-bar-chart',
    ],
    [
        'url' => $dateFormatType !== null ? base_url(['timeline', 'chart', 'pie', $date]) : '',
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
        <?php foreach ($menu as $item): ?>
            <?php if (!empty($item['url'])): ?>
                <li href="<?= $item['url']; ?>">
                    <a href="<?= $item['url']; ?>">
                        <span class="<?= $item['icon']; ?>" aria-hidden="true"></span>&nbsp;
                        <?= $item['label']; ?>
                    </a>
                </li>
            <?php endif; ?>
        <?php endforeach; ?>
    </ul>
</div>
