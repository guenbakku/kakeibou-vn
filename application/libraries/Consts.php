<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Consts
{
    public const VERSION = '3.0.0';

    public const ERR_BAD_REQUEST = 'Yêu cầu không hợp lệ';

    public const ERR_NOT_FOUND = 'Không tìm thấy dữ liệu';

    public const ERR_LOGIN_INFO_INVALID = 'Username hoặc password không đúng';

    public const ERR_USER_LOCKED = 'Tài khoản đã bị khóa do nhập sai mật khẩu quá số lần quy định.<br>Vui lòng thử lại sau khoản %s phút nữa.';

    public const ERR_CATEGORY_NOT_EMPTY = 'Không xóa được danh mục <strong>%s</strong>.<br>Cần xóa hết dữ liệu thu chi của danh mục này trước khi xóa.';

    public const ERR_CATEGORY_RESTRICT_DELETE = 'Danh mục <strong>%s</strong> không được phép xóa.';

    public const ERR_ACCOUNT_NOT_EMPTY = 'Không xóa được tài khoản <strong>%s</strong>.<br>Cần xóa hết dữ liệu thu chi của tài khoản này trước khi xóa.';

    public const ERR_ACCOUNT_RESTRICT_DELETE = 'Tài khoản <strong>%s</strong> không được phép xóa.';

    public const ERR_TRANSFER_FROM_TO_SAME = 'Giá trị <strong>Chuyển từ</strong> và <strong>đến</strong> không được giống nhau.';

    public const SUCC_ADD_INOUT_RECORD = 'Thêm ghi chép <strong>%s</strong> thành công. <a href="%s">Sửa lại</a>';

    public const SUCC_EDIT_INOUT_RECORD = 'Sửa ghi chép thành công. <a href="%s">Sửa lại</a>';

    public const SUCC_DELETE_INOUT_RECORD = 'Xóa ghi chép thành công';

    public const SUCC_EDIT_SETTING = 'Sửa thiết đặt thành công';

    public const SUCC_EDIT_CATEGORY = 'Sửa danh mục thành công';

    public const SUCC_EDIT_CATEGORY_ORDER = 'Sửa thứ tự danh mục thành công';

    public const SUCC_EDIT_MONTH_ESTIMATED_OUTGO = 'Sửa dự định chi tháng này thành công';

    public const SUCC_ADD_CATEGORY = 'Thêm danh mục thành công';

    public const SUCC_DEL_CATEGORY = 'Xóa danh mục thành công';

    public const SUCC_EDIT_ACCOUNT_ORDER = 'Sửa thứ tự tài khoản thành công';

    public const SUCC_EDIT_ACCOUNT = 'Sửa tài khoản thành công';

    public const SUCC_ADD_ACCOUNT = 'Thêm tài khoản thành công';

    public const SUCC_DEL_ACCOUNT = 'Xóa tài khoản thành công';

    public const LABEL = [
        'submit' => 'Nhập',
        'submit_continue' => 'Nhập và tiếp tục',
        'edit' => 'Sửa',
        'delete' => 'Xóa',
        'login' => 'Đăng nhập',
        'search' => 'Tìm',
    ];

    public array $storage = [];

    public function __construct()
    {
        $this->storage = [
            'version' => '3.0.0',
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
        return $this->storage[$path] ?? $default;
    }
}
