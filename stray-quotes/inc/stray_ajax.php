<?php

// If your 'wp-content' directory is not in the default location you have to enter the path to your blog here. Example: '/home/www/public_html/wp'
$changedDir = ''; 

if($_POST['action'] == 'newquote'){

	if (!$changedDir)$changedDir = preg_replace('|wp-content.*$|','', __FILE__);
	include_once($changedDir.'/wp-config.php');
	
	$categories = isset($_POST['categories'])?$_POST['categories']:'';
	$sequence = isset($_POST['sequence'])?$_POST['sequence']:'';
	$linkphrase = isset($_POST['linkphrase'])?$_POST['linkphrase']:'';
	$widgetid = isset($_POST['widgetid'])?$_POST['widgetid']:'';
	$multi = isset($_POST['multi'])?$_POST['multi']:'';
	$offset = isset($_POST['offset'])?$_POST['offset']:'';
	$timer = isset($_POST['timer'])?$_POST['timer']:'';
	$sort = isset($_POST['sort'])?$_POST['sort']:'';
	$orderby = isset($_POST['orderby'])?$_POST['orderby']:'';
	$disableaspect = isset($_POST['disableaspect'])?$_POST['disableaspect']:'';
	$contributor = isset($_POST['contributor'])?$_POST['contributor']:'';

	echo get_stray_quotes($categories,$sequence,$linkphrase,$multi,$timer,false,$offset,$widgetid,false,$orderby,$sort,'',$disableaspect,$contributor);
}

?>