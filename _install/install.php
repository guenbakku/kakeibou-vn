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

// Danh sách những file cần thay đổi.
// Đường dẫn tương đối so với file php này.
$files = array(
    '../.htaccess',
    '../index.php',
    '../index.php',
    '../application/config/database.php',
    '../application/config/database.php',
    '../application/config/database.php',
    '../application/config/autoload.php',
);

// Những chỗ cần thay đổi trong từng file.
// Thứ tự dưới đây tương ứng với thứ tự file ở array $files
$patterns = array(
    "#RewriteBase (.*)#",
    "#'BASEURL', '(.*)'#",
    "#'ENVIRONMENT'.*'(development)#",
    "#'database'.*=>.*'(.*)'#",
    "#'username'.*=>.*'(.*)'#",
    "#'password'.*=>.*'(.*)'#",
    "#(,\s+'kint-0.9/kint')#",
); 

// Nội dung muốn thay đổi
// Thứ tự dưới đây tương ứng với thứ tự file ở array $files
$replaces = array(
    $APP_PATH,
    $APP_URL,
    'production',
    'kakeibou',
    'guest',
    'm4tkh4uDatabase',
    '',
);

if (count($files) != count($patterns) || count($files) != count($replaces)){
    die('ERROR: Dinh nghia thay doi khong hop le!');
}

// Thực hiện thay đổi
echo 'APP_URL: '.$APP_URL.'<hr>';
foreach($files as $i => $file){
    $content = replace_specific($patterns[$i], $replaces[$i], file_get_contents($file));
    file_put_contents($file, $content);
    echo $file.' -> OK<br>';
}
echo '<hr>SET URL CHO APP THANH CONG';

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
function replace_specific($pattern, $replace, $content)
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