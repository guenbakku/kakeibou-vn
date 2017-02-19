<?php

$config = array(
    
    // Validation for Login
    'user/edit/password' => array(
        array(
            'field' => 'old_password',
            'label' => 'Mật khẩu hiện tại',
            'rules' => 'required|callback__password_matched',
        ),
        array(
            'field' => 'new_password',
            'label' => 'Mật khẩu mới',
            'rules' => 'required|min_length[6]',
        ),
        array(
            'field' => 'new_password_confirm',
            'label' => 'Mật khẩu mới (xác nhận)',
            'rules' => 'required|min_length[6]|matches[new_password]',
        ),
    ),
    
    // Validation for Login
    'user/login' => array(
        array(
            'field' => 'username',
            'label' => 'Username',
            'rules' => 'trim|required|max_length[32]|xss_clean|htmlspecialchars',
        ),
        array(
            'field' => 'password',
            'label' => 'Password',
            'rules' => 'required',
        ),
    ),
    
    // For inout
    'inout/add'=> array(
        array(
            'field' => 'amount',
            'label' => 'Số tiền',
            'rules' => 'required|is_natural_no_zero|xss_clean',
        ),
        array(
            'field' => 'memo',
            'label' => 'Ghi chú',
            'rules' => 'strip_tags',
        ),
    ),
    
    'inout/edit'=> array(
        array(
            'field' => 'amount',
            'label' => 'Số tiền',
            'rules' => 'required|is_natural_no_zero|xss_clean',
        ),
        array(
            'field' => 'memo',
            'label' => 'Ghi chú',
            'rules' => 'strip_tags',
        ),
    ),
    
    // For category
    'category/add' => array(
        array(
            'field' => 'name',
            'label' => 'Tên danh mục',
            'rules' => 'required|trim|xss_clean',
        ),
    ),
    
    'category/edit' => array(
        array(
            'field' => 'name',
            'label' => 'Tên danh mục',
            'rules' => 'required|trim|xss_clean',
        ),
    ),
);

