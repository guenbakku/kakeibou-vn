<script type="text/javascript" src="<?=base_url()?>asset/upload/jquery-ui-1.12.0.custom/jquery.ui.touch-punch.min.js"></script>
<script type="text/javascript">
    $(function(){
        $('#sortable').sortable({
            handle: '.handle',
            items: '.sort',
            axis: 'y',
            start: function(event, ui) {
                var start_pos = ui.item.index();
                ui.item.data('start_pos', start_pos);
            },
            update: function(event, ui) {
                var index = ui.item.index();
                var start_pos = ui.item.data('start_pos');
                
                //update the html of the moved item to the current index
                $('#sortable tr:nth-child(' + (index + 1) + ')').find('input[data-role=order_no]').val(index);
                
                if (start_pos < index) {
                    //update the items before the re-ordered item
                    for(var i=index; i > 0; i--){
                        $('#sortable tr:nth-child(' + i + ')').find('input[data-role=order_no]').val(i - 1);
                    }
                }else {
                    //update the items after the re-ordered item
                    for(var i=index+2;i <= $("#sortable tr").length; i++){
                        $('#sortable tr:nth-child(' + i + ')').find('input[data-role=order_no]').val(i - 1);
                    }
                }
            },
        });
    });
</script>

<?=$this->template->get_view('elements/page-nav')?>
<div class="container">
    <?php echo form_open($url['form'])?>
        <div class="panel panel-default">
            <table class="table table-bordered" style="border-bottom:1px solid; border-color:inherit">
                <tr>
                    <td>
                        <div id="subNav" class="btn-group navigation">
                            <a class="btn btn-default" href="<?=$url['subNav'][0]?>">Thu</a>
                            <a class="btn btn-default" href="<?=$url['subNav'][1]?>">Chi</a>
                        </div>
                        <a class="btn btn-default pull-right" href="<?=$url['add']?>"><span class="glyphicon glyphicon-plus"></span></a>
                    </td>
                </tr>
            </table>
            
            <?php if (count($categories) > 0): ?>
            <table id="sortable" class="table table-bordered table-ex">
                <?php foreach ($categories as $i => $item): ?>
                <tr class="sort">
                    <td style="width:30px" class="handle">
                        <span class="glyphicon glyphicon-menu-hamburger"></span>
                    </td>
                    <?php if ($inout_type_id == 2): ?>
                    <td style="width:30px">
                        <input type="hidden" value="0" name="<?=$field_name = 'categories['.$item['id'].'][month_fixed_money]'?>">
                        <?=form_checkbox(
                            array(
                                'name'      => $field_name,
                                'value'     => '1',
                                'checked'   => (bool)$item['month_fixed_money'],
                            )
                        )?>
                    </td>
                    <?php endif ?>
                    <td>
                        <div class="pull-right">
                            <span class="glyphicon glyphicon-menu-right"></span>
                        </div>
                        <a href="<?=sprintf($url['edit'], $item['id'])?>" style="display:block; padding-right:15px; color:inherit">
                            <?=$item['name']?>
                        </a>
                        <input type="hidden" name="categories[<?=$item['id']?>][id]" value="<?=$item['id']?>">
                        <input type="hidden" name="categories[<?=$item['id']?>][order_no]" data-role="order_no" value="<?=$item['order_no']?>">
                    </td>
                </tr>
                <?php endforeach ?>
            </table>
            <table class="table table-bordered" style="border-top:1px solid; border-color:inherit">
                <tr>
                    <td>
                        <input type="submit" class="btn btn-primary" value="Lưu">
                    </td>
                </tr>
            </table>
            <?php else: ?>
            <p class="text-center" style="margin-top:10px">Chưa có dữ liệu</p>
            <?php endif ?>
            
        </div>
    </form>
</div>