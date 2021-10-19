<?php

$config = [
    
    'user/edit/info' => [
        [
            'field' => 'fullname',
            'label' => 'Tên',
            'rules' => 'trim|required|max_length[128]|xss_clean|htmlspecialchars',
        ],
    ],

    'user/edit/password' => [
        [
            'field' => 'old_password',
            'label' => 'Mật khẩu hiện tại',
            'rules' => 'required|callback__password_matched',
        ],
        [
            'field' => 'new_password',
            'label' => 'Mật khẩu mới',
            'rules' => 'required|min_length[6]',
        ],
        [
            'field' => 'new_password_confirm',
            'label' => 'Mật khẩu mới (xác nhận)',
            'rules' => 'required|min_length[6]|matches[new_password]',
        ],
    ],
    
    // Validation for Login
    'user/login' => [
        [
            'field' => 'username',
            'label' => 'Username',
            'rules' => 'trim|required|max_length[32]|xss_clean|htmlspecialchars',
        ],
        [
            'field' => 'password',
            'label' => 'Password',
            'rules' => 'required',
        ],
    ],
    
    // For inout
    'inout/add'=> [
        [
            'field' => 'amount',
            'label' => 'Số tiền',
            'rules' => 'required|is_natural_no_zero|xss_clean',
        ],
        [
            'field' => 'memo',
            'label' => 'Ghi chú',
            'rules' => 'strip_tags',
        ],
    ],
    
    'inout/edit'=> [
        [
            'field' => 'amount',
            'label' => 'Số tiền',
            'rules' => 'required|is_natural_no_zero|xss_clean',
        ],
        [
            'field' => 'memo',
            'label' => 'Ghi chú',
            'rules' => 'strip_tags',
        ],
    ],
    
    // For category
    'category/add' => [
        [
            'field' => 'name',
            'label' => 'Tên danh mục',
            'rules' => 'required|trim|xss_clean',
        ],
    ],
    
    'category/edit' => [
        [
            'field' => 'name',
            'label' => 'Tên danh mục',
            'rules' => 'required|trim|xss_clean',
        ],
    ],
    
    // For account
    'account/add' => [
        [
            'field' => 'name',
            'label' => 'Tên tài khoản',
            'rules' => 'required|trim|xss_clean',
        ],
    ],
    
    'account/edit' => [
        [
            'field' => 'name',
            'label' => 'Tên tài khoản',
            'rules' => 'required|trim|xss_clean',
        ],
    ],
];

