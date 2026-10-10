<?= $this->template->get_view('elements/page-nav'); ?>
<div class="container">
    <?= form_open($url['form'], ['class' => 'form-vertical']); ?>
        <div class="panel panel-default">
            <div class="panel-body">
                <?php foreach ($categories as $i => $category) { ?>
                <div class="form-group">
                    <?= form_input(
                        [
                            'name' => $field_name = sprintf('categories[%d][id]', $i),
                            'type' => 'hidden',
                        ],
                        $category['id']
                    ); ?>
                    <?= form_input(
                        [
                            'name' => $field_name = sprintf('categories[%d][name]', $i),
                            'type' => 'hidden',
                        ],
                        $category['name']
                    ); ?>
                    <div class="row">
                        <div class="col-xs-12">
                            <label><?= $category['name']; ?></label>
                        </div>
                        <div class="col-xs-12">
                            <div class="input-group">
                                <?= form_input(
                                    [
                                        'name' => $field_name = sprintf('categories[%d][month_estimated_amount]', $i),
                                        'type' => 'text',
                                    ],
                                    set_value($field_name, null),
                                    [
                                        'class' => 'form-control amount',
                                    ]
                                ); ?>
                                <span class="input-group-addon"><?= settings('currency'); ?></span>
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <label>
                                <?= form_input([
                                                'name' => sprintf('categories[%d][is_month_fixed_money]', $i),
                                                'type' => 'hidden',
                                                'value' => '0',
                                            ]); ?>
                                <?= form_checkbox([
                                                'name' => sprintf('categories[%d][is_month_fixed_money]', $i),
                                                'value' => '1',
                                                'checked' => (bool) $category['is_month_fixed_money'],
                                            ]); ?>
                                Khoảng chi cố định
                            </label>
                            <i class="fa fa-info-circle text-muted ml-1" data-toggle="popover" data-placement="top" data-content="Không tính vào &quot;Số tiền có thể chi&quot; hàng tháng" style="cursor:pointer"></i>
                        </div>
                    </div>
                </div>
                <?php } ?>

                <button type="submit" onClick="submitForm(this)" class="btn btn-primary"><?= settings('label.submit'); ?></button>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/autonumeric@4.5.4"></script>
<script type="text/javascript">
    $(function () {
        $('[data-toggle="popover"]').popover({
            'container': 'body',
            'placement': 'top',
            'trigger': 'click',
        });

        $('body').on('click', function (e) {
            $('[data-toggle="popover"]').each(function () {
                if (!$(this).is(e.target)
                    && $(this).is('[aria-describedby]')
                    && $('.popover').has(e.target).length === 0) {
                    $(this).popover('hide');
                }
            });
        });
    });

    anElements = new AutoNumeric.multiple('.amount', {
        formatOnPageLoad: true,
        decimalPlaces: 0,
    });
    function submitForm(btn) {
        anElements.forEach(elm => elm.unformat());
        Cashbook.submitButton(btn, 'submit');
    }
</script>
