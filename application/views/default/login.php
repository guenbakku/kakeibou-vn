<div class="container">
    <div class="panel panel-default">
        <div class="panel-heading"><strong>Login</strong></div>
        <div class="panel-body">
            
            <form class="form-vertical" method="post" action="<?=base_url()?>login/doLogin">
                <div class="form-group">
                    <label class="sr-only" for="username">Username</label>
                    <input type="text" class="form-control" id="username" name="username" placeholder="Username">
                </div>
                <div class="form-group">
                    <label class="sr-only" for="password">Password</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Password">
                </div>
                <div class="check-box">
                    <label>
                        <input type="checkbox" name="remember" value="1"> Ghi nhớ đăng nhập
                    </label>
                </div>
                <input type="submit" class="btn btn-primary" value="Đăng nhập">
            </form>
            
        </div>
    </div>
</div>

