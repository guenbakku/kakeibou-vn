<script type="text/javascript">
    $(function(){
        $('.scrollToFixed').scrollToFixed({marginTop: 70});
        $('.submit-on-change').change(function(){
            $(this).parents('form').submit();
        });
    });
</script>

<div class="container scrollToFixed" style="background: #fff; margin-top:-20px; padding-top:20px;">
    <h4 class="text-center" style="margin-top:0"><?=$date?></h4>
    <div class="well well-sm">
        <?=form_open($url['form'], array('method'=>'get', 'id' => 'addCashFlow', 'class' => 'form-horizon'))?>
            <div class="row">
                <div class="col-xs-6">
                    <label>Tài khoản</label>
                    <?=form_dropdown(
                        'account', 
                        $select['accounts'] + array('0' => 'Thực thu chi'), 
                        $account, 
                        array(
                            'class' => 'form-control submit-on-change',
                        )
                    )?>
                </div>
                <div class="col-xs-6">
                    <label>Phụ trách</label>
                    <?=form_dropdown(
                        'player', 
                        $select['players'] + array('0' => 'Tất cả'), 
                        $player, 
                        array(
                            'class' => 'form-control submit-on-change',
                        )
                    )?>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="container" style="margin-top:-20px">
    <?=$this->template->get_view('viewlist/inouts_of_day_list')?>
</div>