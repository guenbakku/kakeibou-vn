<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading"><strong>Login</strong></div>
        <div class="panel-body">
            
            <?php echo form_open(base_url().'user/login', array('class' => 'form-vertical'))?>
                <div class="form-group">
                    <label class="sr-only" for="username">Username</label>
                    <?=form_input(
                        array(
                            'name'        => $field_name = 'username',
                            'type'        => 'text',
                            'placeholder' => 'Username',
                        ),
                        set_value($field_name, null),
                        array(
                            'class' => 'form-control'
                        )
                    )?>
                </div>
                <div class="form-group">
                    <label class="sr-only" for="password">Password</label>
                    <?=form_input(
                        array(
                            'name'        => $field_name = 'password',
                            'type'        => 'password',
                            'placeholder' => 'Password',
                        ),
                        set_value($field_name, null),
                        array(
                            'class' => 'form-control'
                        )
                    )?>
                </div>
                <div class="check-box">
                    <label>
                        <input type="checkbox" name="remember" value="1" checked> Ghi nhớ đăng nhập
                    </label>
                </div>
                <input type="submit" class="btn btn-primary" value="Đăng nhập">
            </form>
            
        </div>
    </div>
</div>

