<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Constants extends CI_Model {
    
    const ERR_BAD_REQUEST = 'Yêu cầu không hợp lệ';
    
    const ERR_NOT_FOUND = 'Không tìm thấy dữ liệu';
    
    const SUCC_ADD_INOUT_RECORD = 'Thêm ghi chép <strong>%s</strong> thành công';
    
    const SUCC_EDIT_INOUT_RECORD = 'Sửa ghi chép thành công';
    
    const SUCC_DELETE_INOUT_RECORD = 'Xóa ghi chép thành công';
    
    const SUCC_EDIT_SETTING = 'Sửa thiết đặt thành công';
    
}