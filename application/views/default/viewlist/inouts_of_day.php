<script type="text/javascript">
    $(function(){
        $('.submit-on-change').change(function(){
            $(this).parents('form').submit();
        });
    });
</script>

<div class="container">
    <h4 class="text-center"><?=$date?></h4>
    <div class="well">
        <?=form_open($form_url, array('method'=>'get', 'id' => 'addCashFlow', 'class' => 'form-horizon'))?>
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

<div class="container">
    <?=$this->template->get_view('viewlist/inouts_of_day_list')?>
</div>