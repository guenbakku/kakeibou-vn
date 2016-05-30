<?php echo form_open($form_url, array('id' => 'addCashFlow', 'class' => 'container'))?>
    <div class="panel panel-default">
        <div class="panel-heading"><strong><?=$title?></strong></div>
        <div class="panel-body">
            <div class="form-group">
                <label>Số tiền:</label>
                <input class="form-control" type="number" name="amount">
            </div>
            <div class="form-group">
                <label>Thời điểm:</label>
                <input class="form-control" type="date" name="time">
            </div>
            <div class="form-group">
                <label>Danh mục:</label>
                <select class="form-control" name="category">
                    <option value="1">Ăn uống</option>
                    <option value="2">Đi lại</option>
                    <option value="3">Khác</option>
                </select>
            </div>
            <div class="form-group">
                <label>Tài khoản:</label>
                <select class="form-control" name="account">
                    <option value="1">Tiền mặt</option>
                    <option value="2">Yucho</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Nhập</button>
            <button type="button" class="btn btn-info" onclick="history.back();return false;">Lui</button>
            <button type="button" class="btn btn-danger pull-right">Xóa</button>
        </div>
    </div>
</form>
    
    