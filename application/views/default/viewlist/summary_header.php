<div class="container">
    <div class="well well-sm">
        <div class="row">
            <div class="col-xs-2">
                <span class="btn btn-default btn-sm pull-left disabled">Ngày</span>
            </div>
            <div class="col-xs-8 text-center">
                <?php if(in_array($mode, array('dayInMonth', 'monthInYear'))): ?>
                <div class="btn-group">
                    <a class="btn btn-primary btn-sm" href="<?=$url['dateChange']['prev']?>">
                        <span class="glyphicon glyphicon-chevron-left"></span>
                    </a>
                    <span class="btn btn-default btn-sm disabled"><?=$date?></span>
                    <a class="btn btn-primary btn-sm" href="<?=$url['dateChange']['next']?>">
                        <span class="glyphicon glyphicon-chevron-right"></span>
                    </a>
                </div>
                <?php endif ?>
            </div>
            <div class="col-xs-2">
                <a class="btn btn-primary btn-sm pull-right" data-toggle="modal" data-target="#date-selection">
                    <span class="glyphicon glyphicon-calendar"></span>
                </a>
            </div>
        </div>
        <!-- <div class="btn-group btn-group-justified navigation">
            <a class="btn btn-default" href="<?=$url['btnGroup']['day']?>">Ngày</a>
            <a class="btn btn-default" href="<?=$url['btnGroup']['month']?>">Tháng</a>
            <a class="btn btn-default" href="<?=$url['btnGroup']['year']?>">Năm</a>
        </div> -->
    </div>
</div>

<div class="container">
    <ul class="nav nav-tabs navigation">
        <li href="<?=$url['navTabs']['list']?>"><a href="<?=$url['navTabs']['list']?>">Danh sách</a></li>
        <li href="<?=$url['navTabs']['chart']?>"><a href="<?=$url['navTabs']['chart']?>">Biểu đồ</a></li>
    </ul>
</div>
<br>

<!-- Date selecting modal -->
<div class="modal fade" id="date-selection" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <?=form_open($url['form'], array('method'=>'get', 'id' => 'form', 'class' => 'form-horizon'))?>
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Lựa chọn hiển thị</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-12">
                            <label>Kiểu danh sách</label>
                            <?=form_dropdown(
                                $field_name = 'mode',
                                array(
                                    'dayInMonth' => 'Ngày trong tháng',
                                    'monthInYear' => 'Tháng trong năm',
                                    'year' => 'Năm',
                                ), 
                                $mode, 
                                array(
                                    'id' => 'date-selection-mode',
                                    'class' => 'form-control',
                                )
                            )?>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-xs-6" id="date-selection-year">
                            <label>Năm</label>
                            <?=form_dropdown(
                                $field_name = 'year', 
                                $select['year'], 
                                $year?? date('Y'), 
                                array(
                                    'class' => 'form-control',
                                )
                            )?>
                        </div>
                        <div class="col-xs-6" id="date-selection-month">
                            <label>Tháng</label>
                            <?=form_dropdown(
                                $field_name = 'month', 
                                $select['month'], 
                                $month?? date('m'), 
                                array(
                                    'class' => 'form-control',
                                )
                            )?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="text-align:center">
                    <button type="submit" class="btn btn-primary">Nhập</button>
                    <button type="button" class="btn btn-default" data-dismiss="modal">Đóng</button>
                </div>
            </div>
        <?=form_close()?>
    </div>
</div>

<script type="text/javascript">
    $(function(){     
        $('#date-selection-mode').change(function(){
            switch($(this).val()) {
                case 'dayInMonth': 
                    $('#date-selection-year')
                        .show()
                        .find('select').prop('disabled', false);
                    $('#date-selection-month')
                        .show()
                        .find('select').prop('disabled', false);
                    break;
                case 'monthInYear':
                    $('#date-selection-year')
                        .show()
                        .find('select').prop('disabled', false);
                    $('#date-selection-month')
                        .hide()
                        .find('select').prop('disabled', true);
                    break;
                default:
                    $('#date-selection-year')
                        .hide()
                        .find('select').prop('disabled', true);
                    $('#date-selection-month')
                        .hide()
                        .find('select').prop('disabled', true);
            }
            
            return false;
        }).trigger('change');
        
        $('#form [type=submit]').click(function(evt){
            evt.preventDefault();
            
            var form = $(this).parents('form');
            
            var dateArr = [
                $('#date-selection-year').find('select:enabled').val(),
                $('#date-selection-month').find('select:enabled').val(),
            ];
            
            dateArr = dateArr.filter(function(item){
                return typeof item !== 'undefined';
            });
            
            var url = form.attr('action');
            form.attr('action', url + '/' + dateArr.join('-'));
            window.location.href = url + '/' + dateArr.join('-');
        });
    });
</script>
