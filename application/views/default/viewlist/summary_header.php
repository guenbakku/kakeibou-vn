<div class="container">
    <div class="well well-sm">
        <div class="row">
            <div class="col-xs-2">
                <a class="btn btn-default btn-sm pull-left" href="<?=$url['back']?>">
                    <span class="glyphicon glyphicon-menu-left"></span>
                </a>
            </div>
            <div class="col-xs-8 text-center">
                <?php if(in_array($mode, array('dayInMonth', 'monthInYear'))): ?>
                <div class="btn-group">
                    <a class="btn btn-primary btn-sm" href="<?=$url['dateChange']['prev']?>">
                        <span class="glyphicon glyphicon-triangle-left"></span>
                    </a>
                    <span class="btn btn-default btn-sm disabled">
                        <?=$date?>
                    </span>
                    <a class="btn btn-primary btn-sm" href="<?=$url['dateChange']['next']?>">
                        <span class="glyphicon glyphicon-triangle-right"></span>
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
        <?=form_open($url['dateSelectionForm'], array('method'=>'get', 'id'=>'dateSelectionForm', 'class'=>'form-horizon'))?>
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
