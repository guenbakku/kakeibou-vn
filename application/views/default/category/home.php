<script type="text/javascript" src="<?= asset_url('upload/jquery-ui-1.12.0.custom/jquery.ui.touch-punch.min.js'); ?>"></script>
<script type="text/javascript" src="<?= asset_url('upload/js/app.sortableWrapper.js'); ?>"></script>
<script type="text/javascript">
    $(function(){
        $('.sortable').sortableWrapper();
    });
</script>

<?= $this->template->get_view('elements/page-nav'); ?>
<div class="container">
    <?= form_open($url['form']); ?>
        <div class="panel panel-default">
            <table class="table table-bordered" style="border-bottom:1px solid; border-color:inherit">
                <tr>
                    <td>
                        <div id="subNav" class="btn-group navigation">
                            <a class="btn btn-default" href="<?= $url['subNav'][0]; ?>">Thu</a>
                            <a class="btn btn-default" href="<?= $url['subNav'][1]; ?>">Chi</a>
                        </div>
                        <a class="btn btn-default pull-right" href="<?= $url['add']; ?>"><span class="glyphicon glyphicon-plus"></span></a>
                    </td>
                </tr>
            </table>

            <?php if (count($categories) > 0) { ?>
            <table class="table table-bordered table-ex sortable">
                <?php foreach ($categories as $i => $item) { ?>
                <tr class="sort">
                    <td style="width:30px" class="handle">
                        <span class="glyphicon glyphicon-menu-hamburger"></span>
                    </td>
                    <td style="border-right:0; width:100%">
                        <a href="<?= sprintf($url['edit'], $item['id']); ?>" style="display:block; color:inherit; text-decoration:none">
                            <?= $item['name']; ?>
                        </a>
                        <input type="hidden" name="categories[<?= $item['id']; ?>][id]" value="<?= $item['id']; ?>">
                        <input type="hidden" name="categories[<?= $item['id']; ?>][order_no]" data-role="order_no" value="<?= $i; ?>">
                    </td>
                    <td style="border-left:0; padding-right:15px">
                        <span class="glyphicon glyphicon-menu-right"></span>
                    </td>
                </tr>
                <?php } ?>
            </table>
            <table class="table table-bordered" style="border-top:1px solid; border-color:inherit">
                <tr>
                    <td>
                        <button type="submit" class="btn btn-primary" onClick="Cashbook.submitButton(this, 'submit')"><?= settings('label.submit'); ?></button>
                    </td>
                </tr>
            </table>
            <?php } else { ?>
            <p class="text-center" style="margin-top:10px">Chưa có dữ liệu</p>
            <?php } ?>
        </div>
    </form>
</div>
