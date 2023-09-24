<?=$this->template->get_view('elements/page-nav')?>
<div class="container">
    <?php echo form_open($url['form'], array('id' => 'addCashFlow', 'class' => 'form-vertical'))?>
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="form-group">
                    <label>Số tiền:</label>
                    <div class="input-group">
                        <span class="input-group-addon"><?=$inout_type_sign?></span>
                        <?=form_input(
                            array(
                                'name' => $field_name = 'amount',
                                'type' => 'text',
                            ),
                            set_value($field_name, null),
                            array(
                                'class' => 'form-control amount',
                            )
                        )?>
                        <span class="input-group-addon"><?=APP_CURRENCY?></span>
                    </div>
                </div>
                <div class="form-group">
                    <label>Thời gian:</label>
                    <?=form_input(
                        array(
                            'name' => $field_name = 'date',
                            'type' => 'date',
                        ),
                        set_value($field_name, date('Y-m-d')),
                        array(
                            'class' => 'form-control',
                        )
                    )?>
                </div>

                <div class="form-group">
                    <label>Ghi chú:</label>
                    <?=form_input(
                        $field_name = 'memo',
                        set_value($field_name, null, false),
                        array(
                            'class' => 'form-control autocomplete',
                        )
                    )?>
                </div>

                <?php if (in_array($type, array('outgo', 'income'))): ?>
                <div class="form-group">
                    <label>Danh mục:</label>
                    <?=form_dropdown(
                        $field_name = 'category_id',
                        $select['categories'],
                        set_value($field_name, null),
                        array(
                            'class' => 'form-control',
                        )
                    )?>
                </div>
                <?php endif ?>

                <?php if (in_array($type, array('outgo', 'income'))): ?>
                <div class="row">
                    <div class="col-xs-6" style="padding-right: 7.5px">
                        <div class="form-group">
                            <label>Tài khoản:</label>
                            <?=form_dropdown(
                                $field_name = 'account_id',
                                $select['accounts'],
                                set_value($field_name, null),
                                array(
                                    'class' => 'form-control',
                                )
                            )?>
                        </div>
                    </div>
                    <div class="col-xs-6" style="padding-left: 7.5px">
                        <div class="form-group">
                            <label>Phụ trách:</label>
                            <?=form_dropdown(
                                $field_name = 'player',
                                $select['players'],
                                set_value($field_name, $this->auth->user('id')),
                                array(
                                    'class' => 'form-control',
                                )
                            )?>
                        </div>
                    </div>
                </div>
                <?php endif ?>

                <?php if (in_array($type, array('internal'))): ?>
                <div class="row">
                    <div class="col-xs-9">
                        <div class="row">
                            <div class="col-xs-6" style="padding-right: 0">
                                <div class="form-group">
                                    <label>Chuyển từ:</label>
                                    <?=form_dropdown(
                                        $field_name = 'transfer_from',
                                        $select['transfer'],
                                        set_value($field_name, element(0, array_keys($select['transfer']))),
                                        array(
                                            'class' => 'form-control',
                                        )
                                    )?>
                                </div>
                            </div>
                            <div class="col-xs-6" style="padding-right: 0">
                                <div class="form-group">
                                    <label>đến:</label>
                                    <?=form_dropdown(
                                        $field_name = 'transfer_to',
                                        $select['transfer'],
                                        set_value($field_name, element(1, array_keys($select['transfer']))),
                                        array(
                                            'class' => 'form-control',
                                        )
                                    )?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-3">
                        <div class="form-group">
                            <label>　</label>
                            <button id="switch-account" type="button" class="btn btn-default form-control"><i class="glyphicon glyphicon-retweet"></i></button>
                        </div>
                    </div>
                </div>
                <?php endif ?>

                <?php if (in_array($type, array('outgo'))): ?>
                    <div class="form-group">
                        <label>
                            <input type="hidden" value="0" name="<?=$field_name = 'skip_month_estimated'?>">
                            <?=form_checkbox(
                                array(
                                    'name'      => $field_name,
                                    'value'     => '1',
                                    'checked'   => (bool)set_value($field_name, false),
                                )
                            )?>
                            Không tính vào Dự định chi tháng này
                        </label>
                    </div>
                <?php endif ?>

                <button type="button" onClick="submitForm(this)" class="btn btn-primary"><?=Consts::LABEL['submit']?></button>
                <?php if ($this->router->fetch_method() == 'add'): ?>
                    <button type="button" onClick="submitFormAndContinue(this)" class="btn btn-primary"><?=Consts::LABEL['submit_continue']?></button>
                <?php endif ?>
                <?php if ($this->router->fetch_method() == 'edit'): ?>
                    <button type="button" onClick="Cashbook.submitbutton(this, 'delete')" class="btn btn-danger pull-right"><?=Consts::LABEL['delete']?></button>
                <?php endif ?>
            </div>
        </div>
    </form>
</div>

<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/autonumeric@4.5.4"></script>
<script type="text/javascript">
    // Format number typed in amount input
    anElements = new AutoNumeric.multiple('.amount', {
        formatOnPageLoad: true,
        decimalPlaces: 0,
    });
    function submitForm(btn) {
        anElements.forEach(elm => elm.unformat());
        Cashbook.submitbutton(btn, 'submit');
    }
    function submitFormAndContinue(btn) {
        anElements.forEach(elm => elm.unformat());
        Cashbook.submitbutton(btn, 'continue');
    }
</script>

<script type="text/javascript">
    $(function(){
        const cash_flow = "<?=$type?>";

        // Search memo
        $("[name=memo]").autocomplete({
            source: function(req, resp) {
                $.getJSON("/inout/search_memo", {
                    keyword: req.term,
                    cash_flow,
                }, resp);
            },
            select: function(even, ui) {
                const {category_id, account_id} = ui.item;
                if (category_id) {
                    $("[name=category_id]").val(category_id).trigger('change');
                }
                if (account_id) {
                    $("[name=account_id]").val(account_id).trigger('change');
                }
            },
            minLength: 2,
        });

        // Tự động check skip_month_estimated
        $("[name=category_id]").change(function (evt) {
            if ($("[name=skip_month_estimated]").length === 0) {
                return false;
            }
            const category_id = $(this).val();
            $.getJSON("/category/is_month_fixed_money", {
                'id': category_id
            }).done(function (data) {
                $("[name=skip_month_estimated]").prop('checked', data);
            });
        });

        // Switch giá trị transfer-from và transfer-to
        $("#switch-account").on("click", () => {
            const transfer_from = $("[name=transfer_from]").val();
            const transfer_to = $("[name=transfer_to]").val();
            $("[name=transfer_from]").val(transfer_to);
            $("[name=transfer_to]").val(transfer_from);
        });
    });
</script>
