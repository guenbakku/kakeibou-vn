<div class="row">
    <div class="col-xs-6" id="date-selection-year">
        <div class="form-group">
            <label>Năm</label>
            <?= form_dropdown(
                $field_name = 'year',
                ['' => '--'] + $select['year'],
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