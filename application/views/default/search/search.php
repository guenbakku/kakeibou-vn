<?=$this->template->get_view('search/form')?>
<?php if ($list !== null):?>
    <div class="container">
        <?php if ($total_items > 0): ?>
            <p>Tìm được <?=$total_items?> kết quả</p>
        <?php endif ?>
        <?=$this->template->get_view('viewlist/inouts_of_day_list')?>
    </div>
<?php endif ?>