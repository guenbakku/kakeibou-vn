<div class="container">
    <div class="well well-sm">
        <div class="row">
            <div class="col-xs-2">
                <a class="btn btn-primary pull-left" data-toggle="modal" data-target="#date-selection">
                    <span class="glyphicon glyphicon-calendar"></span>
                </a>
            </div>
            <div class="col-xs-10">
                <?php if(in_array($mode, array('dayInMonth', 'monthInYear'))): ?>
                <div class="btn-group">
                    <a class="btn btn-primary" href="<?=$url['dateChange']['prev']?>">
                        <span class="glyphicon glyphicon-chevron-left"></span>
                    </a>
                    <span class="btn btn-default disabled"><?=$date?></span>
                    <a class="btn btn-primary" href="<?=$url['dateChange']['next']?>">
                        <span class="glyphicon glyphicon-chevron-right"></span>
                    </a>
                </div>
                <?php endif ?>
            </div>
        </div>
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
        <?=form_open($url['form'], array('method'=>'get', 'id'=>'date-selection-form', 'class'=>'form-horizon'))?>
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Lựa chọn thời gian</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-6" id="date-selection-year">
                            <label>Năm</label>
                            <?=form_dropdown(
                                $field_name = 'year', 
                                array('' => '') + $select['year'], 
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
                                array('' => '') + $select['month'], 
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
        $('#date-selection-form [name=year]').switcher({
            targets         : ['#date-selection-form [name=month]'],
            disableValues   : [''],
        });
        
        $('#date-selection-form [type=submit]').click(function(evt){
            evt.preventDefault();
            
            var form = $(this).parents('form');
            
            var dateArr = [
                $('#date-selection-year').find('select:enabled').val(),
                $('#date-selection-month').find('select:enabled').val(),
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
