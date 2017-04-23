<div class="row">
    <div class="col-xs-4" style="padding-right:0px">
        <div class="form-group">
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
    </div>
    <div class="col-xs-4" style="padding-right:7.5px">
        <div class="form-group">
            <label>Tháng</label>
            <?=form_dropdown(
                $field_name = 'month', 
                array('' => '--') + $select['month'], 
                $month?? date('m'), 
                array(
                    'class' => 'form-control',
                )
            )?>
        </div>
    </div>
    <div class="col-xs-4" style="padding-left:7.5px">
        <div class="form-group">
            <label>Ngày</label>
            <?=form_dropdown(
                $field_name = 'day', 
                array('' => '--') + $select['day'], 
                $day?? date('d'), 
                array(
                    'class' => 'form-control',
                )
            )?>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(function(){     
        $('#dateSelectionForm [name=month]').switcher({
            targets         : ['#dateSelectionForm [name=day]'],
            disableValues   : [''],
        });
        
        $('#dateSelectionForm [type=submit]').click(function(evt){
            evt.preventDefault();
            
            var form = $(this).parents('form');
            
            var dateArr = [
                form.find('select[name=year]:enabled').val(),
                form.find('select[name=month]:enabled').val(),
                form.find('select[name=day]:enabled').val(),
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