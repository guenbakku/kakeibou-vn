<?php

/*
 *==============================================================================
 * Thay đổi thiết lập cơ bản để app có thể chạy được ở những máy khác nhau.
 *==============================================================================
 */

define('INSTALL_PATH', '_install/install.php');

$DOMAIN   = $_SERVER['SERVER_NAME'];
$APP_PATH = str_replace(INSTALL_PATH, '', $_SERVER['PHP_SELF']);
$APP_URL  = 'http://'.$DOMAIN.$APP_PATH;

// Danh sách file và những chỗ cần thay đổi trong file đó
// Đường dẫn tương đối so với file php này.
$files = array(
    '../.htaccess' => array(
        "#RewriteBase (.*)#"    => $APP_PATH,
    ),
);

// Thực hiện thay đổi
echo 'APP_URL: '.$APP_URL.'<hr>';
foreach($files as $file => $replacements) {
    foreach ($replacements as $pattern => $replace) {
        $content = replace($pattern, $replace, file_get_contents($file));
        file_put_contents($file, $content);
    }
    echo $file.' -> OK<br>';
}
echo '<hr>SET ENVIRONMENT THANH CONG';

/*
 *--------------------------------------------------------------------
 * Thay đổi chỗ được chỉ định cụ thể
 * Hàm này ngược với preg_replace thông thường ở chỗ sẽ thay đổi phần 
 * được chỉ định trong cặp dấu ngoặc ()
 *
 * @param   string : regex, bao gồm dấu () để chỉ định chỗ cần được thay
 * @param   string : chuỗi muốn thay thế
 * @param   string : nội dung gốc chứa chỗ muốn thay thế
 * @return  string : nội dung đã thực hiện thay thế
 *--------------------------------------------------------------------
 */
function replace($pattern, $replace, $content)
{
    // Lật ngược cặp ngoặc trong pattern để đánh dấu phần cần giữ lại
    // Thêm ngoặc đóng 2 đầu
    // Mặc định delimitor chỉ là một ký tự
    $pt = $pattern;
    $pt = str_replace('(', '((', $pt);
    $pt = str_replace(')', '(', $pt);
    $pt = str_replace('((', ')', $pt);
    $pt = substr_replace($pt, '(', 1, 0);
    $pt = substr_replace($pt, ')', -1, 0);
    return preg_replace($pt, "$1".$replace."$2", $content);
}