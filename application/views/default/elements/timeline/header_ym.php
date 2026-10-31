<?php if (in_array($dateFormatType, ['ym', 'y'])) { ?>
<div class="btn-group">
    <a class="btn btn-primary btn-sm" href="<?= $url['dateChange']['prev']; ?>">
        <span class="glyphicon glyphicon-triangle-left"></span>
    </a>
    <a class="btn btn-default btn-sm disabled">
        <?= $date; ?>
    </a>
    <a class="btn btn-primary btn-sm" href="<?= $url['dateChange']['next']; ?>">
        <span class="glyphicon glyphicon-triangle-right"></span>
    </a>
</div>
<?php } else { ?>
<div class="btn-group">
    <a class="btn btn-primary btn-sm disabled" href="<?= $url['dateChange']['prev']; ?>">
        <span class="glyphicon glyphicon-triangle-left"></span>
    </a>
    <a class="btn btn-default btn-sm disabled">
        ----
    </a>
    <a class="btn btn-primary btn-sm disabled" href="<?= $url['dateChange']['next']; ?>">
        <span class="glyphicon glyphicon-triangle-right"></span>
    </a>
</div>
<?php } ?>
<a class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#date-selection">
    <span class="glyphicon glyphicon-calendar"></span>
</a>

<!-- Date selecting modal -->
<div class="modal" id="date-selection" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <?= form_open($url['dateSelectionForm'], ['method' => 'get', 'id' => 'dateSelectionForm', 'class' => 'form-horizon']); ?>
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Lựa chọn thời gian</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-6" id="date-selection-year">
                            <div class="form-group">
                                <label>Năm</label>
                                <?= form_dropdown(
                                    $field_name = 'year',
                                    ['' => '--'] + array_reverse($select['year'], true),
                                    $year ?? date('Y'),
                                    [
                                        'class' => 'form-control',
                                    ]
                                ); ?>
                            </div>
                        </div>
                        <div class="col-xs-6" id="date-selection-month">
                            <div class="form-group">
                                <label>Tháng</label>
                                <?= form_dropdown(
                                    $field_name = 'month',
                                    ['' => '--'] + $select['month'],
                                    $month ?? date('m'),
                                    [
                                        'class' => 'form-control',
                                    ]
                                ); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="text-align:center">
                    <button type="submit" class="btn btn-primary" onClick="Cashbook.submitButton(this, 'submit')">Chọn</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        <?= form_close(); ?>
    </div>
</div>

<script type="text/javascript">
    $(function(){
        $('#dateSelectionForm [name=year]').switcher({
            targets         : ['#dateSelectionForm [name=month]'],
            disableValues   : [''],
        });

        $('#dateSelectionForm [type=submit]').click(function(evt){
            evt.preventDefault();

            var form = $(this).parents('form');

            var dateArr = [
                form.find('select[name=year]:enabled').val(),
                form.find('select[name=month]:enabled').val(),
            ];

            dateArr = dateArr.filter(function(item){
                return typeof item !== 'undefined' && item !== '';
            });

            var url = form.attr('action');
            url = url + '/' + dateArr.join('-');
            window.location.href = url;
        });
    });
</script>
