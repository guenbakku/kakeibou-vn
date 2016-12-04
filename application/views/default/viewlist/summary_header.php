<script type="text/javascript">
$(function(){
    $('.submit-on-change').change(function(){
        $(this).parents('form').submit();
    });
});
</script>

<div class="container">
    <div class="btn-group btn-group-justified navigation">
        <a class="btn btn-default" href="<?=$url['btnGroup']['day']?>">Ngày</a>
        <a class="btn btn-default" href="<?=$url['btnGroup']['month']?>">Tháng</a>
        <a class="btn btn-default" href="<?=$url['btnGroup']['year']?>">Năm</a>
    </div>
</div>
<br>

<div class="container">
    <?php if (in_array($mode, array('day', 'month'))): ?>
    <div class="well well-sm">
        <?=form_open($url['form'], array('method'=>'get', 'id' => 'addCashFlow', 'class' => 'form-horizon'))?>
            <div class="row">
                <div class="col-xs-6">
                    <label>Năm</label>
                    <?=form_dropdown(
                        $field_name = 'year', 
                        $select['year'], 
                        $year, 
                        array(
                            'class' => 'form-control submit-on-change',
                        )
                    )?>
                </div>
                <?php if ($mode == 'day'): ?>
                <div class="col-xs-6">
                    <label>Tháng</label>
                    <?=form_dropdown(
                        $field_name = 'month', 
                        $select['month'], 
                        $month, 
                        array(
                            'class' => 'form-control submit-on-change',
                        )
                    )?>
                </div>
                <?php endif ?>
            </div>
        </form>
    </div>
    <?php endif ?>

    <ul class="nav nav-tabs navigation">
        <li href="<?=$url['navTabs']['list']?>"><a href="<?=$url['navTabs']['list']?>">Danh sách</a></li>
        <li href="<?=$url['navTabs']['chart']?>"><a href="<?=$url['navTabs']['chart']?>">Biểu đồ</a></li>
    </ul>
</div>
<br>
