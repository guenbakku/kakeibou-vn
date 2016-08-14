<script type="text/javascript" src="<?=base_url()?>asset/upload/jquery-ui-1.12.0.custom/jquery.ui.touch-punch.min.js"></script>
<script type="text/javascript">
    navigation('#subNav', 0);

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
        
        $('input').click(function(){
            $(this).focus();
        });
    });
</script>

<div class="container">
    <?php echo form_open($form_url)?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <strong>Quản lý danh mục</strong>
            </div>
            <table class="table table-bordered" style="border-bottom:1px solid; border-color:inherit">
                <tr>
                    <td>
                        <div id="subNav" class="btn-group">
                            <a class="btn btn-default" href="?inout_type_id=1">Thu</a>
                            <a class="btn btn-default" href="?inout_type_id=2">Chi</a>
                        </div>
                        <a class="btn btn-default pull-right" href="<?=base_url().$this->uri->uri_string()?>/add/?inout_type_id=<?=$inout_type_id?>">Thêm</a>
                    </td>
                </tr>
            </table>
            <table id="sortable" class="table table-bordered table-ex">
                <?php foreach ($categories as $i => $item): ?>
                <tr class="sort">
                    <td style="width:30px"><span class="handle glyphicon glyphicon-menu-hamburger"></span></td>
                    <td>
                        <input type="hidden" name="categories[<?=$item['id']?>][id]" value="<?=$item['id']?>">
                        <input type="hidden" name="categories[<?=$item['id']?>][order_no]" data-role="order_no" value="<?=$item['order_no']?>">
                        <?=$item['name']?>
                    </td>
                    <td style="width:50px"><a class="btn btn-xs btn-info" href="edit/<?=$item['id']?>">Sửa</a></td>
                </tr>
                <?php endforeach ?>
            </table>
            <table class="table table-bordered" style="border-top:1px solid; border-color:inherit">
                <tr>
                    <td>
                        <input type="submit" class="btn btn-primary" value="Lưu thứ tự">
                    </td>
                </tr>
            </table>
        </div>
    </form>
</div>