<?= $this->template->get_view('elements/page-nav.php'); ?>
<?= $this->template->get_view('elements/timeline/header', ['date_element' => 'header_ymd']); ?>

<script type="text/javascript">
    $(function(){
        var infinite = new Waypoint.Infinite({
            element: $('.infinite-container'),
            items: '.infinite-item',
        })
    });
</script>

<div class="container">
    <div class="well well-sm">
        <?= form_open($url['subForm'], ['method' => 'get', 'id' => 'subForm', 'class' => 'form-horizon']); ?>
            <div class="row">
                <div class="col-xs-6">
                    <label>Tài khoản</label>
                    <?= form_dropdown(
                        'account',
                        $select['accounts'] + ['0' => 'Thực thu chi', '-1' => 'Tất cả'],
                        $account_id,
                        [
                            'class' => 'form-control submit-on-change',
                        ]
                    ); ?>
                </div>
                <div class="col-xs-6">
                    <label>Phụ trách</label>
                    <?= form_dropdown(
                        'player',
                        $select['players'] + ['0' => 'Tất cả'],
                        $player_id,
                        [
                            'class' => 'form-control submit-on-change',
                        ]
                    ); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-xs-12">
                    <label class="mt-3 mb-0">
                        <input type="hidden" value="0" name="<?= $field_name = 'only_show_temp_inout'; ?>">
                        <?= form_checkbox(
                            [
                                'name' => $field_name,
                                'value' => '1',
                                'checked' => (bool) $only_show_temp_inout,
                                'class' => 'submit-on-change',
                            ]
                        ); ?>
                        Chỉ hiện dữ liệu Danh nghĩa
                    </label>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="container">
    <div class="infinite-container">
        <?php if ($current_num > 0) { ?>
            <?php for ($i = 0; $i < $current_num; ++$i) { ?>
                <?php if ($i == 0 || $result[$i]['date'] != $result[$i - 1]['date']) { ?>
                    <div class="list-group infinite-item">
                        <div class="list-group-item active">
                            <strong><?= $result[$i]['date']; ?> (<?= day_of_week($result[$i]['date']); ?>)</strong>
                        </div>
                <?php } ?>
                        <!-- Item -->
                        <a class="list-group-item" href="<?= sprintf($url['editTemplate'], $result[$i]['id']); ?>">
                            <div style="padding-right:15px; position:absolute; right:0px">
                                <span class="glyphicon glyphicon-menu-right"></span>
                            </div>
                            <div class="row">
                                <div class="col-xs-6" style="padding-right:0">
                                    <div><?= $result[$i]['category']; ?></div>
                                    <div class="small text-muted">
                                        <span class="label label-default" style="margin-right:3px">
                                            <span class="fa <?= $result[$i]['account_icon']; ?>"></span> <?= $result[$i]['account_icon'] == 'fa-bank' ? 'Tài khoản' : 'Tiền mặt'; ?>
                                        </span>
                                        <?php if ($result[$i]['is_temp']) { ?>
                                        <span class="label label-default">
                                            Danh nghĩa
                                        </span>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="col-xs-5 text-right">
                                    <div class="<?= $result[$i]['inout_type'] == 'Thu' ? 'text-income' : 'text-outgo'; ?>"><?= currency($result[$i]['amount']); ?></div>
                                    <div class="label <?= $result[$i]['player_label']; ?>"><?= $result[$i]['player']; ?></div>
                                </div>
                            </div>
                            <div class="row" style="margin-top: 5px ">
                                <div class="col-xs-11">
                                    <div class="small text-muted">
                                        <em ><?= $result[$i]['memo']; ?></em>
                                    </div>
                                </div>
                            </div>
                        </a>
                <?php if ($i == $current_num - 1 || $result[$i]['date'] != $result[$i + 1]['date']) { ?>
                    </div>
                <?php } ?>
            <?php } ?>
        <?php } else { ?>
            <div class="list-group">
                <span class="list-group-item"><?= settings('err_not_found'); ?></span>
            </div>
        <?php } ?>
    </div>
    <?php if ($url['next_page'] !== null) { ?>
    <a class="infinite-more-link sr-only" href="<?= $url['next_page']; ?>">Trang tiếp</a>
    <?php } ?>
</div>
