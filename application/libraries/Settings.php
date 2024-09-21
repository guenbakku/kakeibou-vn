<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Settings
{
    public array $storage = [];

    public function __construct()
    {
        $this->storage = [
            'version' => '3.0.0',
            'currency' => $_ENV['CURRENCY'] ?? 'VND',
            'err_bad_request' => 'Yêu cầu không hợp lệ',
            'err_not_found' => 'Không tìm thấy dữ liệu',
            'err_login_info_invalid' => 'Username hoặc password không đúng',
            'err_user_locked' => 'Tài khoản đã bị khóa do nhập sai mật khẩu quá số lần quy định.<br>Vui lòng thử lại sau khoản %s phút nữa.',
            'err_category_not_empty' => 'Không xóa được danh mục <strong>%s</strong>.<br>Cần xóa hết dữ liệu thu chi của danh mục này trước khi xóa.',
            'err_category_restrict_delete' => 'Danh mục <strong>%s</strong> không được phép xóa.',
            'err_account_not_empty' => 'Không xóa được tài khoản <strong>%s</strong>.<br>Cần xóa hết dữ liệu thu chi của tài khoản này trước khi xóa.',
            'err_account_restrict_delete' => 'Tài khoản <strong>%s</strong> không được phép xóa.',
            'err_transfer_from_to_same' => 'Giá trị <strong>Chuyển từ</strong> và <strong>đến</strong> không được giống nhau.',
            'succ_add_inout_record' => 'Thêm ghi chép <strong>%s</strong> thành công. <a href="%s">Sửa lại</a>',
            'succ_edit_inout_record' => 'Sửa ghi chép thành công. <a href="%s">Sửa lại</a>',
            'succ_delete_inout_record' => 'Xóa ghi chép thành công',
            'succ_edit_setting' => 'Sửa thiết đặt thành công',
            'succ_edit_category' => 'Sửa danh mục thành công',
            'succ_edit_category_order' => 'Sửa thứ tự danh mục thành công',
            'succ_edit_month_estimated_outgo' => 'Sửa dự định chi tháng này thành công',
            'succ_add_category' => 'Thêm danh mục thành công',
            'succ_del_category' => 'Xóa danh mục thành công',
            'succ_edit_account_order' => 'Sửa thứ tự tài khoản thành công',
            'succ_edit_account' => 'Sửa tài khoản thành công',
            'succ_add_account' => 'Thêm tài khoản thành công',
            'succ_del_account' => 'Xóa tài khoản thành công',
            'label' => [
                'submit' => 'Nhập',
                'submit_continue' => 'Nhập và tiếp tục',
                'edit' => 'Sửa',
                'delete' => 'Xóa',
                'login' => 'Đăng nhập',
                'search' => 'Tìm',
            ],
        ];
    }

    public function get(string $path, $default = null)
    {
        return array_get($this->storage, $path, $default);
    }
}
