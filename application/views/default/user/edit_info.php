<?=$this->template->get_view('elements/page-nav')?>

<div class="container">
    <div class="panel panel-default">
        <div class="panel-body">
            <?php echo form_open(base_url('setting/user/edit/info'), array('class' => 'form-vertical'))?>
                <div class="form-group">
                    <label for="old_password">Tên:</label>
                    <?=form_input(
                        array(
                            'name' => $field_name = 'fullname',
                            'id'   => $field_name,
                            'type' => 'text',
                        ),
                        set_value($field_name, null),
                        array(
                            'class' => 'form-control'
                        )
                    )?>
                </div>
                <input type="submit" class="btn btn-primary" value="<?=Consts::LABEL['submit']?>">
            </form>
        </div>
    </div>
</div>

