<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Consts {
    
    const VERSION = '3.0';
    
    const RELEASE_DATE = '20170506';
    
    const ERR_BAD_REQUEST = 'Yêu cầu không hợp lệ';
    
    const ERR_NOT_FOUND = 'Không tìm thấy dữ liệu';
    
    const ERR_LOGIN_INFO_INVALID = 'Username hoặc password không đúng';
    
    const ERR_ACCOUNT_LOCKED = 'Tài khoản đã bị khóa do nhập sai mật khẩu quá số lần quy định.<br>Vui lòng thử lại sau khoản %s phút nữa.';
    
    const ERR_CATEGORY_NOT_EMPTY = 'Không xóa được danh mục <strong>%s</strong>.<br>Cần xóa hết dữ liệu thu chi của danh mục này trước khi xóa danh mục.';
    
    const ERR_CATEGORY_RESTRICT_DELETE = 'Danh mục <strong>%s</strong> không được phép xóa.';
    
    const SUCC_ADD_INOUT_RECORD = 'Thêm ghi chép <strong>%s</strong> thành công';
    
    const SUCC_EDIT_INOUT_RECORD = 'Sửa ghi chép thành công';
    
    const SUCC_DELETE_INOUT_RECORD = 'Xóa ghi chép thành công';
    
    const SUCC_EDIT_SETTING = 'Sửa thiết đặt thành công';
    
    const SUCC_EDIT_CATEGORY = 'Sửa danh mục thành công';
    
    const SUCC_EDIT_CATEGORY_ORDER = 'Sửa danh sách danh mục thành công';
    
    const SUCC_EDIT_MONTH_ESTIMATED_OUTGO = 'Sửa dự định chi tháng này thành công';
    
    const SUCC_ADD_CATEGORY = 'Thêm danh mục thành công';
    
    const SUCC_DEL_CATEGORY = 'Xóa danh mục thành công';
    
    const LABEL = [
        'submit'            => 'Nhập',
        'submit_continue'   => 'Nhập và tiếp tục',
        'edit'              => 'Sửa',
        'delete'            => 'Xóa',
        'login'             => 'Đăng nhập',
        'search'            => 'Tìm',
    ];
}