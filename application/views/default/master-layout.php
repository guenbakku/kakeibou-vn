<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>

<!-- Mobile first -->
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="vi">
	<head>
		<title><?=$_page_title?></title>
        <meta http-equiv="Content-Type" content="Text/html; charset=utf-8" />
        <meta name="robots" content="noindex" />
        <meta name="description" content="<?=$_page_description?>" />
        <meta name="keywords" content="cashbook, kakeibou" />
        <meta name="copyright" content="© NVB" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    
        <link rel="shortcut icon" href="<?=template_url()?>images/favicon.gif" type="image/gif">
        <link rel="apple-touch-icon" href="<?=template_url()?>images/favicon.gif" />
        <link rel="stylesheet" type="text/css" media="screen,print" href="<?=base_url()?>asset/upload/bootstrap-3.3.6-dist/css/bootstrap.min.css">
        <link rel="stylesheet" type="text/css" media="screen,print" href="<?=base_url()?>asset/upload/font-awesome-4.6.3/css/font-awesome.min.css">
        <link rel="stylesheet" type="text/css" media="screen,print" href="<?=base_url()?>asset/upload/jquery-ui-1.12.0.custom/jquery-ui.min.css">
        <link rel="stylesheet" type="text/css" media="screen,print" href="<?=base_url()?>asset/upload/jquery-ui-1.12.0.custom/jquery-ui.structure.min.css">
        <link rel="stylesheet" type="text/css" media="screen,print" href="<?=base_url()?>asset/upload/jquery-ui-1.12.0.custom/jquery-ui.theme.min.css">
        <link rel="stylesheet" type="text/css" media="screen,print" href="<?=template_url()?>css/theme.css">
        <link rel="stylesheet" type="text/css" media="screen,print" href="<?=template_url()?>css/main.css">
        <?=$_styles?>
        
        <script type="text/javascript" src="<?=base_url()?>asset/upload/js/jquery-1.12.3.min.js"></script>
        <script type="text/javascript" src="<?=base_url()?>asset/upload/bootstrap-3.3.6-dist/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="<?=base_url()?>asset/upload/jquery-ui-1.12.0.custom/jquery-ui.min.js"></script>
        <script type="text/javascript" src="<?=base_url()?>asset/upload/js/app.js"></script>
        <script type="text/javascript" src="<?=base_url()?>asset/upload/js/app.utilities.js"></script>
        <script type="text/javascript" src="<?=base_url()?>asset/upload/js/app.navigation.js"></script>
        <script type="text/javascript" src="<?=base_url()?>asset/upload/js/app.page-scroll.js"></script>
        <script type="text/javascript" src="<?=base_url()?>asset/upload/js/app.switcher.js"></script>
        <script type="text/javascript" src="<?=base_url()?>asset/upload/waypoints/jquery.waypoints.min.js"></script>
        <script type="text/javascript" src="<?=base_url()?>asset/upload/waypoints/infinite.min.js"></script>
        <?=$_scripts?>
	</head>

	<body>
        <?=$this->template->get_view('main-nav')?>
        <div class="container"><?=$this->flash->output()?></div>
        <?=$MAIN?>
        <?=$this->template->get_view('footer')?>
    </body>

</html>