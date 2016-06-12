<?php

$config = array(
    
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
            'rules' => 'required|max_length[32]|xss_clean|htmlspecialchars',
        ),
    ),
    
    // For inout add
    'inout/add'=> array(
        array(
            'field' => 'amount',
            'label' => 'Số tiền',
            'rules' => 'required|is_natural_no_zero|xss_clean',
        ),
    ),
);

