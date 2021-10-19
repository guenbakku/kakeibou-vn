<div class="container">
    <div class="page-nav">
        <div class="row">
            <div class="col-xs-2">
                <a class="btn btn-default btn-sm" href="<?=$url['back']?>">
                    <span class="glyphicon glyphicon-menu-left"></span>
                </a>
            </div>
            <div class="col-xs-10">
                <strong>Xem danh sách</strong>
            </div>
        </div>
    </div>
</div>

<div class="container">
    <ul class="nav nav-tabs navigation">
        <li href="<?=$url['navTabs']['summary']?>"><a href="<?=$url['navTabs']['summary']?>">Thống kê</a></li>
        <li href="<?=$url['navTabs']['detail']?>"><a href="<?=$url['navTabs']['detail']?>">Chi tiết</a></li>
    </ul>
</div>

<div class="container">
    <?=form_open($url['dateSelectionForm'], array('method'=>'get', 'id'=>'dateSelectionForm', 'class'=>'form-vertical'))?>
        <div class="panel panel-default no-top-border">
            <div class="panel-body">
                <?=$this->template->get_view('elements/timeline/date_selection_' . $date_selection_form)?>
                <button type="submit" class="btn btn-primary">Chọn</button>
            </div>
        </div>
    <?=form_close()?>
</div>
    