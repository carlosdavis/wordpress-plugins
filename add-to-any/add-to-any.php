<?php
/*
Plugin Name: AddToAny: Share/Bookmark/Email Button
Plugin URI: http://www.addtoany.com/
Description: Help people share, bookmark, and email your posts & pages using any service, such as Facebook, Twitter, Google Buzz, Digg and many more.  [<a href="options-general.php?page=add-to-any.php">Settings</a>]
Version: .9.9.5.9
Author: AddToAny
Author URI: http://www.addtoany.com/
*/

if( !isset($A2A_locale) )
	$A2A_locale = '';

// Pre-2.6 compatibility
if ( !defined('WP_CONTENT_URL') )
	define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
if ( ! defined( 'WP_PLUGIN_URL' ) )
	define( 'WP_PLUGIN_URL', WP_CONTENT_URL . '/plugins' );
	
$A2A_SHARE_SAVE_plugin_basename = plugin_basename(dirname(__FILE__));
$A2A_SHARE_SAVE_plugin_url_path = WP_PLUGIN_URL.'/'.$A2A_SHARE_SAVE_plugin_basename; // /wp-content/plugins/add-to-any

// Fix SSL
if (function_exists('is_ssl') && is_ssl()) // @since 2.6.0
	$A2A_SHARE_SAVE_plugin_url_path = str_replace('http:', 'https:', $A2A_SHARE_SAVE_plugin_url_path);

function A2A_SHARE_SAVE_textdomain() {
	global $A2A_SHARE_SAVE_plugin_url_path, $A2A_SHARE_SAVE_plugin_basename;
	
	load_plugin_textdomain('add-to-any',
		$A2A_SHARE_SAVE_plugin_url_path.'/languages',
		$A2A_SHARE_SAVE_plugin_basename.'/languages');
}
add_filter('init', 'A2A_SHARE_SAVE_textdomain');

function A2A_SHARE_SAVE_link_vars($linkname = FALSE, $linkurl = FALSE) {
	global $post;
	
	$linkname		= ($linkname) ? $linkname : get_the_title($post->ID);
	$linkname_enc	= rawurlencode( $linkname );
	$linkurl		= ($linkurl) ? $linkurl : get_permalink($post->ID);
	$linkurl_enc	= rawurlencode( $linkurl );	
	
	return compact( 'linkname', 'linkname_enc', 'linkurl', 'linkurl_enc' );
}

include_once('services.php');

// Combine ADDTOANY_SHARE_SAVE_ICONS and ADDTOANY_SHARE_SAVE_BUTTON
function ADDTOANY_SHARE_SAVE_KIT( $args = false ) {
	
	if ( ! isset($args['html_container_open']))
		$args['html_container_open'] = "<ul class=\"addtoany_list\">";
	if ( ! isset($args['html_container_close']))
		$args['html_container_close'] = "</ul>";
	// Close container element in ADDTOANY_SHARE_SAVE_BUTTON, not prematurely in ADDTOANY_SHARE_SAVE_ICONS
	$html_container_close = $args['html_container_close']; // Cache for _BUTTON
	unset($args['html_container_close']); // Avoid passing to ADDTOANY_SHARE_SAVE_ICONS since set in _BUTTON
				
	if ( ! isset($args['html_wrap_open']))
		$args['html_wrap_open'] = "<li>";
	if ( ! isset($args['html_wrap_close']))
		$args['html_wrap_close'] = "</li>";
	
    $kit_html = ADDTOANY_SHARE_SAVE_ICONS($args);
	
	$args['html_container_close'] = $html_container_close; // Re-set because unset above for _ICONS
	unset($args['html_container_open']);  // Avoid passing to ADDTOANY_SHARE_SAVE_BUTTON since set in _ICONS
    
	$kit_html .= ADDTOANY_SHARE_SAVE_BUTTON($args);
	
	if($args['output_later'])
		return $kit_html;
	else
		echo $kit_html;
}

function ADDTOANY_SHARE_SAVE_ICONS( $args = array() ) {
	// $args array: output_later, html_container_open, html_container_close, html_wrap_open, html_wrap_close, linkname, linkurl
	
	$linkname = (isset($args['linkname'])) ? $args['linkname'] : FALSE;
	$linkurl = (isset($args['linkurl'])) ? $args['linkurl'] : FALSE;
	
	$args = array_merge($args, A2A_SHARE_SAVE_link_vars($linkname, $linkurl)); // linkname_enc, etc.
		
	global $A2A_SHARE_SAVE_plugin_url_path, $A2A_SHARE_SAVE_services;
	
	// Make available services extensible via plugins, themes (functions.php), etc.
	$A2A_SHARE_SAVE_services = apply_filters('A2A_SHARE_SAVE_services', $A2A_SHARE_SAVE_services);
	
	$active_services = get_option('A2A_SHARE_SAVE_active_services');
	
	$ind_html = "" . $args['html_container_open'];
	
	if( !$active_services )
		$active_services = Array();
	
	foreach($active_services as $active_service) {
		
		// Skip unknown
		if( !in_array($active_service, array_keys($A2A_SHARE_SAVE_services)) )
			continue;

		$service = $A2A_SHARE_SAVE_services[$active_service];
		$safe_name = $active_service;
		$name = $service['name'];
		
		if (isset($service['href'])) {
			$custom_service = TRUE;
			$href = $service['href'];
			$href = str_replace('A2A_LINKURL', $args['linkurl_enc'], $href);
			$href = str_replace('A2A_LINKNAME', $args['linkname_enc'], $href);
		} else {
			$custom_service = FALSE;
		}

		if ( $custom_service && isset($service['icon_url']) )
			$icon = $service['icon_url'];
		elseif ( ! isset($service['icon']))
			$icon = 'default';
		else
			$icon = $service['icon'];
		$width = (isset($service['icon_width'])) ? $service['icon_width'] : '16';
		$height = (isset($service['icon_height'])) ? $service['icon_height'] : '16'; 
		
		$url = ($custom_service) ? $href : "http://www.addtoany.com/add_to/" . $safe_name . "?linkurl=" . $args['linkurl_enc'] . "&amp;linkname=" . $args['linkname_enc'];
		$src = ($custom_service) ? $icon : $A2A_SHARE_SAVE_plugin_url_path."/icons/".$icon.".png";
		
		$link = $args['html_wrap_open']."<a href=\"$url\" title=\"$name\" rel=\"nofollow\" target=\"_blank\">";
		$link .= "<img src=\"$src\" width=\"$width\" height=\"$height\" alt=\"$name\"/>";
		$link .= "</a>".$args['html_wrap_close'];
		
		$ind_html .= apply_filters('addtoany_link', $link);
	}
	
	$ind_html .= (isset($args['html_container_close'])) ? $args['html_container_close'] : "";
	
	if (isset($args['output_later']))
		return $ind_html;
	else
		echo $ind_html;
}

function ADDTOANY_SHARE_SAVE_BUTTON( $args = array() ) {
	
	// $args array = output_later, html_container_open, html_container_close, html_wrap_open, html_wrap_close, linkname, linkurl

	global $A2A_SHARE_SAVE_plugin_url_path, $A2A_SHARE_SAVE_services;
	
	$linkname = (isset($args['linkname'])) ? $args['linkname'] : FALSE;
	$linkurl = (isset($args['linkurl'])) ? $args['linkurl'] : FALSE;
	$html_container_open = (isset($args['html_container_open'])) ? $args['html_container_open'] : '';
	
	// Make available services extensible via plugins, themes (functions.php), etc.
	$A2A_SHARE_SAVE_services = apply_filters('A2A_SHARE_SAVE_services', $A2A_SHARE_SAVE_services);

	$args = array_merge($args, A2A_SHARE_SAVE_link_vars($linkname, $linkurl));; // linkname_enc, etc.
	
	/* AddToAny button */
	
	$button_target	= (get_option('A2A_SHARE_SAVE_button_opens_new_window')=='1' && (get_option('A2A_SHARE_SAVE_onclick')!='1')) ? ' target="_blank"' : '';
	
	if( !get_option('A2A_SHARE_SAVE_button') ) {
		$button_fname	= 'share_save_171_16.png';
		$button_width	= ' width="171"';
		$button_height	= ' height="16"';
		$button_src		= $A2A_SHARE_SAVE_plugin_url_path.'/'.$button_fname;
	} else if( get_option('A2A_SHARE_SAVE_button') == 'CUSTOM' ) {
		$button_src		= get_option('A2A_SHARE_SAVE_button_custom');
		$button_width	= '';
		$button_height	= '';
	} else if( get_option('A2A_SHARE_SAVE_button') == 'TEXT' ) {
		$button_text	= stripslashes(get_option('A2A_SHARE_SAVE_button_text'));
	} else {
		$button_attrs	= explode( '|', get_option('A2A_SHARE_SAVE_button') );
		$button_fname	= $button_attrs[0];
		$button_width	= ' width="'.$button_attrs[1].'"';
		$button_height	= ' height="'.$button_attrs[2].'"';
		$button_src		= $A2A_SHARE_SAVE_plugin_url_path.'/'.$button_fname;
		$button_text	= stripslashes(get_option('A2A_SHARE_SAVE_button_text'));
	}
	
	if( $button_fname == 'favicon.png' || $button_fname == 'share_16_16.png' ) {
		if( !is_feed() ) {
			$style_bg	= 'background:url('.$A2A_SHARE_SAVE_plugin_url_path.'/'.$button_fname.') no-repeat scroll 9px 0px'; // padding-left:9 (9=other icons padding)
			$style_bg	= ';' . $style_bg . ' !important;';
			$style		= ' style="'.$style_bg.'padding:0 0 0 30px;display:inline-block;height:16px;line-height:16px;vertical-align:middle;"'; // padding-left:30+9 (9=other icons padding)
		}
	}
	
	if( $button_text && (!$button_fname || $button_fname == 'favicon.png' || $button_fname == 'share_16_16.png') ) {
		$button			= $button_text;
	} else {
		$style = '';
		$button			= '<img src="'.$button_src.'"'.$button_width.$button_height.' alt="Share/Bookmark"/>';
	}
	
	$button_html = $html_container_open.$args['html_wrap_open'].'<a class="a2a_dd addtoany_share_save" href="http://www.addtoany.com/share_save?'
		.'linkurl='.$args['linkurl_enc']
		.'&amp;linkname='.$args['linkname_enc']
		.'"' . $style . $button_target
		.'>'.$button.'</a>'.$args['html_wrap_close'].$args['html_container_close'];
	
	// If not a feed
	if( !is_feed() ) {
		if (function_exists('is_ssl') ) // @since 2.6.0
			$http_or_https = (is_ssl()) ? 'https' : 'http';
		else
			$http_or_https = 'http';
	
		global $A2A_SHARE_SAVE_external_script_called;
		if ( ! $A2A_SHARE_SAVE_external_script_called ) {
			// Use local cache?
			$cache = (get_option('A2A_SHARE_SAVE_cache')=='1') ? TRUE : FALSE;
			$upload_dir = wp_upload_dir();
			$static_server = ($cache) ? $upload_dir['baseurl'] . '/addtoany' : $http_or_https . '://static.addtoany.com/menu';
			
			// Enternal script call + initial JS + set-once variables
			$initial_js = 'var a2a_config = a2a_config || {};' . "\n";
			$additional_js = get_option('A2A_SHARE_SAVE_additional_js_variables');
			$external_script_call = (($cache) ? 'a2a_config.static_server="' . $static_server . '";' . "\n" : '' )
				. ((get_option('A2A_SHARE_SAVE_onclick')=='1') ? 'a2a_config.onclick=1;' . "\n" : '')
				. ((get_option('A2A_SHARE_SAVE_show_title')=='1') ? 'a2a_config.show_title=1;' . "\n" : '')
				. (($additional_js) ? stripslashes($additional_js) . "\n" : '')
				. "//]]>" . '</script><script type="text/javascript" src="' . $static_server . '/page.js"></script>';
			$A2A_SHARE_SAVE_external_script_called = true;
		}
		else {
			$external_script_call = "a2a.init('page');\n//]]></script>";
			$initial_js = '';
		}
			
		$button_javascript = "\n" . '<script type="text/javascript">' . "//<![CDATA[\n"
			. $initial_js
			. A2A_menu_locale()
			. 'a2a_config.linkname="' . js_escape($args['linkname']) . '";' . "\n"
			. 'a2a_config.linkurl="' . $args['linkurl'] . '";' . "\n"
			. $external_script_call . "\n\n";
		
		$button_html .= $button_javascript;
	
	}
	
	if (isset($args['output_later']))
		return $button_html;
	else
		echo $button_html;
}

if (!function_exists('A2A_menu_locale')) {
	function A2A_menu_locale() {
		global $A2A_locale;
		$locale = get_locale();
		if($locale  == 'en_US' || $locale == 'en' || $A2A_locale != '' )
			return false;
			
		$A2A_locale = 'a2a_localize = {
	Share: "' . __("Share", "add-to-any") . '",
	Save: "' . __("Save", "add-to-any") . '",
	Subscribe: "' . __("Subscribe", "add-to-any") . '",
	Email: "' . __("E-mail", "add-to-any") . '",
    Bookmark: "' . __("Bookmark", "add-to-any") . '",
	ShowAll: "' . __("Show all", "add-to-any") . '",
	ShowLess: "' . __("Show less", "add-to-any") . '",
	FindServices: "' . __("Find service(s)", "add-to-any") . '",
	FindAnyServiceToAddTo: "' . __("Instantly find any service to add to", "add-to-any") . '",
	PoweredBy: "' . __("Powered by", "add-to-any") . '",
	ShareViaEmail: "' . __("Share via e-mail", "add-to-any") . '",
	SubscribeViaEmail: "' . __("Subscribe via e-mail", "add-to-any") . '",
	BookmarkInYourBrowser: "' . __("Bookmark in your browser", "add-to-any") . '",
	BookmarkInstructions: "' . __("Press Ctrl+D or &#8984;+D to bookmark this page", "add-to-any") . '",
	AddToYourFavorites: "' . __("Add to your favorites", "add-to-any") . '",
	SendFromWebOrProgram: "' . __("Send from any e-mail address or e-mail program", "add-to-any") . '",
    EmailProgram: "' . __("E-mail program", "add-to-any") . '"
};
';
		return $A2A_locale;
	}
}

if (!function_exists('A2A_wp_footer_check')) {
	function A2A_wp_footer_check()
	{
		// If footer.php exists in the current theme, scan for "wp_footer"
		$file = get_template_directory() . '/footer.php';
		if( is_file($file) ) {
			$search_string = "wp_footer";
			$file_lines = @file($file);
			
			foreach($file_lines as $line) {
				$searchCount = substr_count($line, $search_string);
				if($searchCount > 0) {
					return true;
					break;
				}
			}
			
			// wp_footer() not found:
			echo "<div class=\"plugin-update\">" . __("Your theme needs to be fixed. To fix your theme, use the <a href=\"theme-editor.php\">Theme Editor</a> to insert <code>&lt;?php wp_footer(); ?&gt;</code> just before the <code>&lt;/body&gt;</code> line of your theme's <code>footer.php</code> file.") . "</div>";
		}
	}  
}

function A2A_SHARE_SAVE_auto_placement($title) {
	global $A2A_SHARE_SAVE_auto_placement_ready;
	$A2A_SHARE_SAVE_auto_placement_ready = true;
	
	return $title;
}


/**
 * Remove the_content filter and add it for next time 
 */
function A2A_SHARE_SAVE_remove_from_content($content) {
	remove_filter('the_content', 'A2A_SHARE_SAVE_to_bottom_of_content', 98);
	add_filter('the_content', 'A2A_SHARE_SAVE_to_bottom_of_content_next_time', 98);
	
	return $content;
}

/**
 * Apply the_content filter "next time"
 */
function A2A_SHARE_SAVE_to_bottom_of_content_next_time($content) {
	add_filter('the_content', 'A2A_SHARE_SAVE_to_bottom_of_content', 98);
	
	return $content;
}


function A2A_SHARE_SAVE_to_bottom_of_content($content) {
	global $A2A_SHARE_SAVE_auto_placement_ready;
	$is_feed = is_feed();
	
	if( ! $A2A_SHARE_SAVE_auto_placement_ready)
		return $content;
	
	if ( 
		( 
			// Tags
			// <!--sharesave--> tag
			strpos($content, '<!--sharesave-->')===false || 
			// <!--nosharesave--> tag
			strpos($content, '<!--nosharesave-->')!==false
		) &&
		(
			// Posts
			// All posts
			( ! is_page() && get_option('A2A_SHARE_SAVE_display_in_posts')=='-1' ) ||
			// Front page posts		
			( is_home() && get_option('A2A_SHARE_SAVE_display_in_posts_on_front_page')=='-1' ) ||
			// Category posts (same as Front page option)
			( is_category() && get_option('A2A_SHARE_SAVE_display_in_posts_on_front_page')=='-1' ) ||
			// Tag Cloud posts (same as Front page option) - WP version 2.3+ only
			( function_exists('is_tag') && is_tag() && get_option('A2A_SHARE_SAVE_display_in_posts_on_front_page')=='-1' ) ||
			// Date-based archives posts (same as Front page option)
			( is_date() && get_option('A2A_SHARE_SAVE_display_in_posts_on_front_page')=='-1' ) ||
			// Author posts (same as Front page option)	
			( is_author() && get_option('A2A_SHARE_SAVE_display_in_posts_on_front_page')=='-1' ) ||
			// Search results posts (same as Front page option)
			( is_search() && get_option('A2A_SHARE_SAVE_display_in_posts_on_front_page')=='-1' ) || 
			// Posts in feed
			( $is_feed && (get_option('A2A_SHARE_SAVE_display_in_feed')=='-1' ) ||
			
			// Pages
			// Individual pages
			( is_page() && get_option('A2A_SHARE_SAVE_display_in_pages')=='-1' ) ||
			// <!--nosharesave-->						
			( (strpos($content, '<!--nosharesave-->')!==false) )
		)
		)
	)	
		return $content;
	
	$kit_args = array(
		"output_later" => true,
		"html_container_open" => ($is_feed) ? "" : "<ul class=\"addtoany_list\">",
		"html_container_close" => ($is_feed) ? "" : "</ul>",
		"html_wrap_open" => ($is_feed) ? "" : "<li>",
		"html_wrap_close" => ($is_feed) ? " " : "</li>",
	);
	
	if ( ! $is_feed ) {
		$container_wrap_open = '<div class="addtoany_share_save_container">';
		$container_wrap_close = '</div>';
	} else {
		$container_wrap_open = '<p>';
		$container_wrap_close = '</p>';
	}
	
	$content .= $container_wrap_open.ADDTOANY_SHARE_SAVE_KIT($kit_args).$container_wrap_close;
	return $content;
}

// Only automatically output button code after the_title has been called - to avoid premature calling from misc. the_content filters (especially meta description)
add_filter('the_title', 'A2A_SHARE_SAVE_auto_placement', 9);
add_filter('the_content', 'A2A_SHARE_SAVE_to_bottom_of_content', 98);

// Excerpts use strip_tags() for the_content, so cancel if Excerpt and append to the_excerpt instead
add_filter('get_the_excerpt', 'A2A_SHARE_SAVE_remove_from_content', 9);
add_filter('the_excerpt', 'A2A_SHARE_SAVE_to_bottom_of_content', 98);


function A2A_SHARE_SAVE_button_css($no_style_tag) {
	if ( ! $no_style_tag) {
	?><style type="text/css">
<?php } ?>
	.addtoany_share_save_container{margin:16px 0;}
	ul.addtoany_list{
		display:inline;
		list-style-type:none;
		margin:0 !important;
		padding:0 !important;
		text-indent:0 !important;
	}
	ul.addtoany_list li{
		background:none !important;
		border:0;
		display:inline !important;
<?php /* For vertical space in the event of wrapping: */ ?>
		line-height:32px;
		list-style-type:none;
		margin:0 !important;
		padding:0 !important;
	}
	ul.addtoany_list li:before{content:"";}
	ul.addtoany_list li a{padding:0 9px;}
	ul.addtoany_list img{
		float:none;
		border:0;
		margin:0;
		padding:0;
		vertical-align:middle;
	}
	ul.addtoany_list a img{
		opacity:.7;
	}
	ul.addtoany_list a:hover img, ul.addtoany_list a.addtoany_share_save img{
		opacity:1;
	}
<?php /* Must declare after "ul.addtoany_list img": */ ?>
	a.addtoany_share_save img{border:0;width:auto;height:auto;}
<?php if ( ! $no_style_tag) { ?>
</style>
<?php
		A2A_SHARE_SAVE_button_css_IE();
	}
}

function A2A_SHARE_SAVE_button_css_IE() {
/* IE support for opacity: */ ?>
<!--[if IE]>
<style type="text/css">
ul.addtoany_list a img{filter:alpha(opacity=70)}
ul.addtoany_list a:hover img,ul.addtoany_list a.addtoany_share_save img{filter:alpha(opacity=100)}
</style>
<![endif]-->
<?php

}

// Use stylesheet?
if (get_option('A2A_SHARE_SAVE_inline_css') != '-1') {
	if (function_exists('wp_enqueue_style')) {
		// WP version 2.1+ only
		wp_enqueue_style('A2A_SHARE_SAVE', $A2A_SHARE_SAVE_plugin_url_path . '/addtoany.min.css', false, '1.0');
	} else {
		// Fallback to inline CSS for WP 2.0
		add_filter('wp_head', 'A2A_SHARE_SAVE_button_css');
	}
	
	// Always output conditional inline CSS stylesheet for IE
	add_filter('wp_head', 'A2A_SHARE_SAVE_button_css_IE');
}



/*****************************
		CACHE ADDTOANY
******************************/

function A2A_SHARE_SAVE_refresh_cache() {
	$contents = wp_remote_fopen("http://www.addtoany.com/ext/updater/files_list/");
	$file_urls = explode("\n", $contents, 20);
	$upload_dir = wp_upload_dir();
	
	// Make directory if needed
	if ( ! wp_mkdir_p( dirname( $upload_dir['basedir'] . '/addtoany/foo' ) ) ) {
		$message = sprintf( __( 'Unable to create directory %s. Is its parent directory writable by the server?' ), dirname( $new_file ) );
		return array( 'error' => $message );
	}
	
	if (count($file_urls) > 0) {
		for ($i = 0; $i < count($file_urls); $i++) {
			// Download files
			$file_url = $file_urls[$i];
			$file_name = substr(strrchr($file_url, '/'), 1, 99);
			
			// Place files in uploads/addtoany directory
			wp_get_http($file_url, $upload_dir['basedir'] . '/addtoany/' . $file_name);
		}
	}
}

function A2A_SHARE_SAVE_schedule_cache() {
	// WP "Cron" requires WP version 2.1
	$timestamp = wp_next_scheduled('A2A_SHARE_SAVE_refresh_cache');
	if ( ! $timestamp) {
		// Only schedule if currently unscheduled
		wp_schedule_event(time(), 'daily', 'A2A_SHARE_SAVE_refresh_cache');
	}
}

function A2A_SHARE_SAVE_unschedule_cache() {
	$timestamp = wp_next_scheduled('A2A_SHARE_SAVE_refresh_cache');
	wp_unschedule_event($timestamp, 'A2A_SHARE_SAVE_refresh_cache');
}



/*****************************
		OPTIONS
******************************/


function A2A_SHARE_SAVE_options_page() {

	global $A2A_SHARE_SAVE_plugin_url_path, $A2A_SHARE_SAVE_services, $wp_version;
	
	// Make available services extensible via plugins, themes (functions.php), etc.
	$A2A_SHARE_SAVE_services = apply_filters('A2A_SHARE_SAVE_services', $A2A_SHARE_SAVE_services);

    if ( isset($_POST['Submit'])) {
		
		// Nonce verification 
		check_admin_referer('add-to-any-dupdate-options');
		
        update_option( 'A2A_SHARE_SAVE_display_in_posts_on_front_page', ($_POST['A2A_SHARE_SAVE_display_in_posts_on_front_page']=='1') ? '1':'-1' );
		update_option( 'A2A_SHARE_SAVE_display_in_posts', ($_POST['A2A_SHARE_SAVE_display_in_posts']=='1') ? '1':'-1' );
		update_option( 'A2A_SHARE_SAVE_display_in_pages', ($_POST['A2A_SHARE_SAVE_display_in_pages']=='1') ? '1':'-1' );
		update_option( 'A2A_SHARE_SAVE_display_in_feed', ($_POST['A2A_SHARE_SAVE_display_in_feed']=='1') ? '1':'-1' );
		update_option( 'A2A_SHARE_SAVE_show_title', ($_POST['A2A_SHARE_SAVE_show_title']=='1') ? '1':'-1' );
		update_option( 'A2A_SHARE_SAVE_onclick', ($_POST['A2A_SHARE_SAVE_onclick']=='1') ? '1':'-1' );
		update_option( 'A2A_SHARE_SAVE_button_opens_new_window', ($_POST['A2A_SHARE_SAVE_button_opens_new_window']=='1') ? '1':'-1' );
		update_option( 'A2A_SHARE_SAVE_button', $_POST['A2A_SHARE_SAVE_button'] );
		update_option( 'A2A_SHARE_SAVE_button_custom', $_POST['A2A_SHARE_SAVE_button_custom'] );
		update_option( 'A2A_SHARE_SAVE_additional_js_variables', trim($_POST['A2A_SHARE_SAVE_additional_js_variables']) );
		update_option( 'A2A_SHARE_SAVE_inline_css', ($_POST['A2A_SHARE_SAVE_inline_css']=='1') ? '1':'-1' );
		update_option( 'A2A_SHARE_SAVE_cache', ($_POST['A2A_SHARE_SAVE_cache']=='1') ? '1':'-1' );
		
		// Schedule cache refresh?
		if ($_POST['A2A_SHARE_SAVE_cache']=='1') {
			A2A_SHARE_SAVE_schedule_cache();
			A2A_SHARE_SAVE_refresh_cache();
		} else {
			A2A_SHARE_SAVE_unschedule_cache();
		}
		
		// Store desired text if 16 x 16px buttons or text-only is chosen:
		if( get_option('A2A_SHARE_SAVE_button') == 'favicon.png|16|16' )
			update_option( 'A2A_SHARE_SAVE_button_text', $_POST['A2A_SHARE_SAVE_button_favicon_16_16_text'] );
		elseif( get_option('A2A_SHARE_SAVE_button') == 'share_16_16.png|16|16' )
			update_option( 'A2A_SHARE_SAVE_button_text', $_POST['A2A_SHARE_SAVE_button_share_16_16_text'] );
		else
			update_option( 'A2A_SHARE_SAVE_button_text', ( trim($_POST['A2A_SHARE_SAVE_button_text']) != '' ) ? $_POST['A2A_SHARE_SAVE_button_text'] : __('Share/Bookmark','add-to-any') );
			
		// Store chosen individual services to make active
		$active_services = Array();
		if( !$_POST['A2A_SHARE_SAVE_active_services'] )
			$_POST['A2A_SHARE_SAVE_active_services'] = Array();
		foreach( $_POST['A2A_SHARE_SAVE_active_services'] as $dummy=>$sitename )
			$active_services[] = substr($sitename, 7);
		update_option('A2A_SHARE_SAVE_active_services', $active_services);
		// Delete then re-add to ensure sorting works (re-sorting doesn't effect associated array equality in PHP so update doesn't hit the dB for identical arrays
		delete_option('A2A_SHARE_SAVE_active_services', $active_services);
		add_option('A2A_SHARE_SAVE_active_services', $active_services);
		
		?>
    	<div class="updated fade"><p><strong><?php _e('Settings saved.'); ?></strong></p></div>
		<?php
		
    }

    ?>
    
    <?php A2A_wp_footer_check(); ?>
    
    <div class="wrap">
	
	<div id="icon-options-general" class="icon32"></div>
	
	<h2><?php _e( 'AddToAny: Share/Save ', 'add-to-any' ) . _e( 'Settings' ); ?></h2>

    <form method="post" action="">
    
	<?php wp_nonce_field('add-to-any-dupdate-options'); ?>
    
        <table class="form-table">
        	<?php if ($wp_version >= "2.6") { /* Must be on WP 2.6+ */ ?>
        	<tr valign="top">
            <th scope="row"><?php _e("Standalone Services", "add-to-any"); ?></th>
			<td><fieldset>
            	<ul id="addtoany_services_sortable" class="addtoany_admin_list">
                	<li class="dummy"><img src="<?php echo $A2A_SHARE_SAVE_plugin_url_path; ?>/icons/transparent.gif" width="16" height="16" alt="" /></li>
                </ul>
                <p id="addtoany_services_info"><?php _e("Choose the services you want below. &nbsp;Click a chosen service again to remove. &nbsp;Reorder services by dragging and dropping as they appear above.", "add-to-any"); ?></p>
            	<ul id="addtoany_services_selectable" class="addtoany_admin_list">
				<?php
					// Show all services
					$active_services = get_option('A2A_SHARE_SAVE_active_services');
					if( !$active_services )
						$active_services = Array();
					
                    foreach ($A2A_SHARE_SAVE_services as $service_safe_name=>$site) { 
						if (isset($site['href']))
							$custom_service = TRUE;
						else
							$custom_service = FALSE;
						if ( ! isset($site['icon']))
							$site['icon'] = 'default';
					?>
                        <li id="a2a_wp_<?php echo $service_safe_name; ?>"
                            title="<?php echo $site['name']; ?>">
                            <span><img src="<?php echo ($custom_service) ? $site['icon_url'] : $A2A_SHARE_SAVE_plugin_url_path.'/icons/'.$site['icon'].'.png'; ?>" width="<?php echo (isset($site['icon_width'])) ? $site['icon_width'] : '16'; ?>" height="<?php echo (isset($site['icon_height'])) ? $site['icon_height'] : '16'; ?>" alt="" /><?php echo $site['name']; ?></span>
                        </li>
				<?php
                    } ?>
                </ul>
            </fieldset></td>
            </tr>
			<?php } ?>
        	<tr valign="top">
            <th scope="row"><?php _e("Button", "add-to-any"); ?></th>
            <td><fieldset>
            	<label>
                	<input name="A2A_SHARE_SAVE_button" value="favicon.png|16|16" type="radio"<?php if(get_option('A2A_SHARE_SAVE_button')=='favicon.png|16|16') echo ' checked="checked"'; ?>
                    	 style="margin:9px 0;vertical-align:middle">
                    <img src="<?php echo $A2A_SHARE_SAVE_plugin_url_path.'/favicon.png'; ?>" width="16" height="16" border="0" style="padding:9px;vertical-align:middle" alt="+ <?php _e('Share/Bookmark','add-to-any'); ?>" title="+ <?php _e('Share/Bookmark','add-to-any'); ?>"
                    	onclick="this.parentNode.firstChild.checked=true"/>
                </label>
                <input name="A2A_SHARE_SAVE_button_favicon_16_16_text" type="text" class="code" size="50" onclick="e=document.getElementsByName('A2A_SHARE_SAVE_button');e[e.length-7].checked=true" style="vertical-align:middle;width:150px"
                	value="<?php echo (get_option('A2A_SHARE_SAVE_button_text') !== FALSE) ? stripslashes(get_option('A2A_SHARE_SAVE_button_text')) : __('Share/Bookmark','add-to-any'); ?>" />
                <label style="padding-left:9px">
                	<input name="A2A_SHARE_SAVE_button" value="share_16_16.png|16|16" type="radio"<?php if(get_option('A2A_SHARE_SAVE_button')=='share_16_16.png|16|16') echo ' checked="checked"'; ?>
                    	 style="margin:9px 0;vertical-align:middle">
                    <img src="<?php echo $A2A_SHARE_SAVE_plugin_url_path.'/share_16_16.png'; ?>" width="16" height="16" border="0" style="padding:9px;vertical-align:middle" alt="+ <?php _e('Share/Bookmark','add-to-any'); ?>" title="+ <?php _e('Share/Bookmark','add-to-any'); ?>"
                    	onclick="this.parentNode.firstChild.checked=true"/>
                </label>
                <input name="A2A_SHARE_SAVE_button_share_16_16_text" type="text" class="code" size="50" onclick="e=document.getElementsByName('A2A_SHARE_SAVE_button');e[e.length-6].checked=true" style="vertical-align:middle;width:150px"
                	value="<?php echo (get_option('A2A_SHARE_SAVE_button_text') !== FALSE) ? stripslashes(get_option('A2A_SHARE_SAVE_button_text')) : __('Share/Bookmark','add-to-any'); ?>" /><br>
                <label>
                	<input name="A2A_SHARE_SAVE_button" value="share_save_120_16.png|120|16" type="radio"<?php if(get_option('A2A_SHARE_SAVE_button' )=='share_save_120_16.png|120|16') echo ' checked="checked"'; ?>
                    	style="margin:9px 0;vertical-align:middle">
                    <img src="<?php echo $A2A_SHARE_SAVE_plugin_url_path.'/share_save_120_16.png'; ?>" width="120" height="16" border="0" style="padding:9px;vertical-align:middle"
                    	onclick="this.parentNode.firstChild.checked=true"/>
                </label><br>
                <label>
                	<input name="A2A_SHARE_SAVE_button" value="share_save_171_16.png|171|16" type="radio"<?php if( !get_option('A2A_SHARE_SAVE_button') || get_option('A2A_SHARE_SAVE_button')=='share_save_171_16.png|171|16' ) echo ' checked="checked"'; ?>
                    	style="margin:9px 0;vertical-align:middle">
                    <img src="<?php echo $A2A_SHARE_SAVE_plugin_url_path.'/share_save_171_16.png'; ?>" width="171" height="16" border="0" style="padding:9px;vertical-align:middle"
                    	onclick="this.parentNode.firstChild.checked=true"/>
                </label><br>
                <label>
                	<input name="A2A_SHARE_SAVE_button" value="share_save_256_24.png|256|24" type="radio"<?php if(get_option('A2A_SHARE_SAVE_button')=='share_save_256_24.png|256|24') echo ' checked="checked"'; ?>
                    	style="margin:9px 0;vertical-align:middle">
                    <img src="<?php echo $A2A_SHARE_SAVE_plugin_url_path.'/share_save_256_24.png'; ?>" width="256" height="24" border="0" style="padding:9px;vertical-align:middle"
                    	onclick="this.parentNode.firstChild.checked=true"/>
				</label><br>
                <label>
                	<input name="A2A_SHARE_SAVE_button" value="CUSTOM" type="radio"<?php if( get_option('A2A_SHARE_SAVE_button') == 'CUSTOM' ) echo ' checked="checked"'; ?>
                    	style="margin:9px 0;vertical-align:middle">
					<span style="margin:0 9px;vertical-align:middle"><?php _e("Image URL"); ?>:</span>
				</label>
  				<input name="A2A_SHARE_SAVE_button_custom" type="text" class="code" size="50" onclick="e=document.getElementsByName('A2A_SHARE_SAVE_button');e[e.length-2].checked=true" style="vertical-align:middle"
                	value="<?php echo get_option('A2A_SHARE_SAVE_button_custom'); ?>" /><br>
				<label>
                	<input name="A2A_SHARE_SAVE_button" value="TEXT" type="radio"<?php if( get_option('A2A_SHARE_SAVE_button') == 'TEXT' ) echo ' checked="checked"'; ?>
                    	style="margin:9px 0;vertical-align:middle">
					<span style="margin:0 9px;vertical-align:middle"><?php _e("Text only"); ?>:</span>
				</label>
                <input name="A2A_SHARE_SAVE_button_text" type="text" class="code" size="50" onclick="e=document.getElementsByName('A2A_SHARE_SAVE_button');e[e.length-1].checked=true" style="vertical-align:middle;width:150px"
                	value="<?php echo ( trim(get_option('A2A_SHARE_SAVE_button_text')) != '' ) ? stripslashes(get_option('A2A_SHARE_SAVE_button_text')) : __('Share/Bookmark','add-to-any'); ?>" />
                
            </fieldset></td>
            </tr>
            <tr valign="top">
            <th scope="row"><?php _e('Placement', 'add-to-any'); ?></th>
            <td><fieldset>
                <label>
                	<input name="A2A_SHARE_SAVE_display_in_posts" 
                    	onclick="e=getElementsByName('A2A_SHARE_SAVE_display_in_posts_on_front_page')[0];f=getElementsByName('A2A_SHARE_SAVE_display_in_feed')[0];
                        	if(!this.checked){e.checked=false;e.disabled=true; f.checked=false;f.disabled=true}else{e.checked=true;e.disabled=false; f.checked=true;f.disabled=false}"
                        onchange="e=getElementsByName('A2A_SHARE_SAVE_display_in_posts_on_front_page')[0];f=getElementsByName('A2A_SHARE_SAVE_display_in_feed')[0];
                        	if(!this.checked){e.checked=false;e.disabled=true; f.checked=false;f.disabled=true}else{e.checked=true;e.disabled=false; f.checked=true;f.disabled=false}"
                        type="checkbox"<?php if(get_option('A2A_SHARE_SAVE_display_in_posts')!='-1') echo ' checked="checked"'; ?> value="1"/>
                	<?php _e('Display Share/Save button at the bottom of posts', 'add-to-any'); ?> <strong>*</strong>
                </label><br/>
                <label>
                	&nbsp; &nbsp; &nbsp; <input name="A2A_SHARE_SAVE_display_in_posts_on_front_page" type="checkbox"<?php 
						if(get_option('A2A_SHARE_SAVE_display_in_posts_on_front_page')!='-1') echo ' checked="checked"';
						if(get_option('A2A_SHARE_SAVE_display_in_posts')=='-1') echo ' disabled="disabled"';
						?> value="1"/>
                    <?php _e('Display Share/Save button at the bottom of posts on the front page', 'add-to-any'); ?>
				</label><br/>
                <label>
                	&nbsp; &nbsp; &nbsp; <input name="A2A_SHARE_SAVE_display_in_feed" type="checkbox"<?php 
						if(get_option('A2A_SHARE_SAVE_display_in_feed')!='-1') echo ' checked="checked"'; 
						if(get_option('A2A_SHARE_SAVE_display_in_posts')=='-1') echo ' disabled="disabled"';
						?> value="1"/>
                    <?php _e('Display Share/Save button at the bottom of posts in the feed', 'add-to-any'); ?>
				</label><br/>
                <label>
                	<input name="A2A_SHARE_SAVE_display_in_pages" type="checkbox"<?php if(get_option('A2A_SHARE_SAVE_display_in_pages')!='-1') echo ' checked="checked"'; ?> value="1"/>
                    <?php _e('Display Share/Save button at the bottom of pages', 'add-to-any'); ?>
				</label>
                <br/><br/>
                <div class="setting-description">
                	<strong>*</strong> <?php _e("If unchecked, be sure to place the following code in <a href=\"theme-editor.php\">your template pages</a> (within <code>index.php</code>, <code>single.php</code>, and/or <code>page.php</code>)", "add-to-any"); ?>: <span id="addtoany_show_template_button_code" class="button-secondary">&#187;</span>
                    <div id="addtoany_template_button_code">
                      <code>&lt;?php if( function_exists('ADDTOANY_SHARE_SAVE_KIT') ) { ADDTOANY_SHARE_SAVE_KIT(); } ?&gt;</code>
                    </div>
                    <noscript><code>&lt;?php if( function_exists('ADDTOANY_SHARE_SAVE_KIT') ) { ADDTOANY_SHARE_SAVE_KIT(); } ?&gt;</code></noscript>
                </div>
            </fieldset></td>
            </tr>
            <tr valign="top">
            <th scope="row"><?php _e('Menu Style', 'add-to-any'); ?></th>
            <td><fieldset>
					<p><?php _e("Using AddToAny's Menu Styler, you can customize the colors of your Share/Save menu! When you're done, be sure to paste the generated code in the <a href=\"#\" onclick=\"document.getElementById('A2A_SHARE_SAVE_additional_js_variables').focus();return false\">Additional Options</a> box below.", "add-to-any"); ?></p>
                    <p>
                		<a href="http://www.addtoany.com/buttons/share_save/menu_style/wordpress" class="button-secondary" title="<?php _e("Open the AddToAny Menu Styler in a new window", "add-to-any"); ?>" target="_blank"
                        	onclick="document.getElementById('A2A_SHARE_SAVE_additional_js_variables').focus();
                            	document.getElementById('A2A_SHARE_SAVE_menu_styler_note').style.display='';"><?php _e("Open Menu Styler", "add-to-any"); ?></a>
					</p>
            </fieldset></td>
            </tr>
            <tr valign="top">
            <th scope="row"><?php _e('Menu Options', 'add-to-any'); ?></th>
            <td><fieldset>
                <label>
                	<input name="A2A_SHARE_SAVE_show_title" 
                        type="checkbox"<?php if(get_option('A2A_SHARE_SAVE_show_title')=='1') echo ' checked="checked"'; ?> value="1"/>
                	<?php _e('Show the title of the post (or page) within the menu', 'add-to-any'); ?>
                </label><br />
				<label>
                	<input name="A2A_SHARE_SAVE_onclick" 
                        type="checkbox"<?php if(get_option('A2A_SHARE_SAVE_onclick')=='1') echo ' checked="checked"'; ?> value="1"
                        onclick="e=getElementsByName('A2A_SHARE_SAVE_button_opens_new_window')[0];if(this.checked){e.checked=false;e.disabled=true}else{e.disabled=false}"
						onchange="e=getElementsByName('A2A_SHARE_SAVE_button_opens_new_window')[0];if(this.checked){e.checked=false;e.disabled=true}else{e.disabled=false}"/>
                	<?php _e('Only show the menu when the user clicks the Share/Save button', 'add-to-any'); ?>
                </label><br />
				<label>
                	<input name="A2A_SHARE_SAVE_button_opens_new_window" 
                        type="checkbox"<?php if(get_option('A2A_SHARE_SAVE_button_opens_new_window')=='1') echo ' checked="checked"'; ?> value="1"
						<?php if(get_option('A2A_SHARE_SAVE_onclick')=='1') echo ' disabled="disabled"'; ?>/>
                	<?php _e('Open the addtoany.com menu page in a new tab or window if the user clicks the Share/Save button', 'add-to-any'); ?>
                </label>
            </fieldset></td>
            </tr>
            <tr valign="top">
            <th scope="row"><?php _e('Additional Options', 'add-to-any'); ?></th>
            <td><fieldset>
            		<p id="A2A_SHARE_SAVE_menu_styler_note" style="display:none">
                        <label for="A2A_SHARE_SAVE_additional_js_variables" class="updated">
                            <strong><?php _e("Paste the code from AddToAny's Menu Styler in the box below!", 'add-to-any'); ?></strong>
                        </label>
                    </p>
                    <label for="A2A_SHARE_SAVE_additional_js_variables">
                    	<p><?php _e('Below you can set special JavaScript variables to apply to each Share/Save menu.', 'add-to-any'); ?>
                    	<?php _e("Advanced users might want to explore AddToAny's <a href=\"http://www.addtoany.com/buttons/customize/\" target=\"_blank\">additional options</a>.", "add-to-any"); ?></p>
					</label>
                    <p>
                		<textarea name="A2A_SHARE_SAVE_additional_js_variables" id="A2A_SHARE_SAVE_additional_js_variables" class="code" style="width: 98%; font-size: 12px;" rows="6" cols="50"><?php echo stripslashes(get_option('A2A_SHARE_SAVE_additional_js_variables')); ?></textarea>
					</p>
                    <?php if( get_option('A2A_SHARE_SAVE_additional_js_variables')!='' ) { ?>
                    <label for="A2A_SHARE_SAVE_additional_js_variables" class="setting-description"><?php _e("<strong>Note</strong>: If you're adding new code, be careful not to accidentally overwrite any previous code.</label>", 'add-to-any'); ?>
					<?php } ?>	
			</fieldset></td>
            </tr>
			<tr valign="top">
            <th scope="row"><?php _e('Advanced Options', 'add-to-any'); ?></th>
            <td><fieldset>
            	<label for="A2A_SHARE_SAVE_inline_css">
					<input name="A2A_SHARE_SAVE_inline_css" id="A2A_SHARE_SAVE_inline_css"
                    	type="checkbox"<?php if(get_option('A2A_SHARE_SAVE_inline_css')!='-1') echo ' checked="checked"'; ?> value="1"/>
            	<?php _e('Use CSS stylesheet', 'add-to-any'); ?> <strong>**</strong>
				</label><br/>
				<label for="A2A_SHARE_SAVE_cache">
					<input name="A2A_SHARE_SAVE_cache" id="A2A_SHARE_SAVE_cache" 
                    	type="checkbox"<?php if(get_option('A2A_SHARE_SAVE_cache')=='1') echo ' checked="checked"'; ?> value="1"/>
            	<?php _e('Cache AddToAny locally with daily cache updates', 'add-to-any'); ?> <strong>***</strong>
				</label>
				<br/><br/>
                <div class="setting-description">
                	<strong>**</strong> <?php _e("If unchecked, be sure to place the CSS in your theme's stylesheet:", "add-to-any"); ?> <span id="addtoany_show_css_code" class="button-secondary">&#187;</span>
					<p id="addtoany_css_code">
						<textarea class="code" style="width:98%;font-size:12px" rows="12" cols="50"><?php A2A_SHARE_SAVE_button_css(TRUE) ?></textarea>
					</p>
					<br/>
					<strong>***</strong> <?php _e("Only consider for sites with frequently returning visitors. Since many visitors will have AddToAny cached in their browser already, serving AddToAny locally from your site will be slower for those visitors.  Be sure to set far future cache/expires headers for image files in your <code>uploads/addtoany</code> directory.", "add-to-any"); ?>
				</div>
            </fieldset></td>
            </tr>
        </table>
        
        <p class="submit">
            <input class="button-primary" type="submit" name="Submit" value="<?php _e('Save Changes', 'add-to-any' ) ?>" />
        </p>
    
    </form>
    
    <h2><?php _e('Like this plugin?','add-to-any'); ?></h2>
    <p><?php _e('<a href="http://wordpress.org/extend/plugins/add-to-any/">Give it a good rating</a> on WordPress.org.','add-to-any'); ?></p>
    <p><?php _e('<a href="http://www.addtoany.com/share_save?linkname=WordPress%20Share%20%2F%20Bookmark%20Plugin%20by%20AddToAny.com&amp;linkurl=http%3A%2F%2Fwordpress.org%2Fextend%2Fplugins%2Fadd-to-any%2F">Share it</a> with your friends.','add-to-any'); ?></p>
    
    <h2><?php _e('Need support?','add-to-any'); ?></h2>
    <p><?php _e('See the <a href="http://wordpress.org/extend/plugins/add-to-any/faq/">FAQs</a>.','add-to-any'); ?></p>
    <p><?php _e('Search the <a href="http://wordpress.org/tags/add-to-any">support forums</a>.','add-to-any'); ?></p>
    </div>

<?php
 
}

// Admin page header
function A2A_SHARE_SAVE_admin_head() {
	if (isset($_GET['page']) && $_GET['page'] == 'add-to-any.php') {
		global $wp_version;
		
		// Must be on WP 2.6+
		if ($wp_version < "2.6")
			return;
	?>
	<script language="JavaScript" type="text/javascript"><!--
	jQuery(document).ready(function(){
	
		var to_input = function(this_sortable){
			// Clear any previous services stored as hidden inputs
			jQuery('input[name="A2A_SHARE_SAVE_active_services[]"]').remove();
			
			var services_array = jQuery(this_sortable).sortable('toArray'),
				services_size = services_array.length;
			if(services_size<1) return;
			
			for(var i=0;i<services_size;i++){
				if(services_array[i]!='') // Exclude dummy icon
					jQuery('form:first').append('<input name="A2A_SHARE_SAVE_active_services[]" type="hidden" value="'+services_array[i]+'"/>');
			}
		};
	
		jQuery('#addtoany_services_sortable').sortable({
			items: 'li:not(#addtoany_show_services, .dummy)',
			placeholder: 'ui-sortable-placeholder',
			opacity: .6,
			tolerance: 'pointer',
			update: function(){to_input(this)}
		});
		
		// Service click = move to sortable list
		var moveToSortableList = function(){
			if( jQuery('#addtoany_services_sortable li').not('.dummy').length==0 )
				jQuery('#addtoany_services_sortable').find('.dummy').hide();
			
			jQuery(this).toggleClass('addtoany_selected')
			.unbind('click', moveToSortableList)
			.bind('click', moveToSelectableList)
			.clone()
			.html( jQuery(this).find('img').clone().attr('alt', jQuery(this).attr('title')) )
			.hide()
			.insertBefore('#addtoany_services_sortable .dummy')
			.fadeIn('fast');
			
			jQuery(this).attr( 'id', 'old_'+jQuery(this).attr('id') );
			
			jQuery('#addtoany_services_sortable li:last').fadeTo('fast', 1);
		};
		
		// Service click again = move back to selectable list
		var moveToSelectableList = function(){
			jQuery(this).toggleClass('addtoany_selected')
			.unbind('click', moveToSelectableList)
			.bind('click', moveToSortableList);
	
			jQuery( '#'+jQuery(this).attr('id').substr(4).replace(/\./, '\\.') )
			.hide('fast', function(){
				jQuery(this).remove();
			});
			
			
			if( jQuery('#addtoany_services_sortable li').not('.dummy').length==1 )
				jQuery('#addtoany_services_sortable').find('.dummy').show();
			
			jQuery(this).attr('id', jQuery(this).attr('id').substr(4));
		};
		
		// Service click = move to sortable list
		jQuery('#addtoany_services_selectable li').bind('click', moveToSortableList);
        
        // Form submit = get sortable list
        jQuery('form').submit(function(){to_input('#addtoany_services_sortable')});
        
        // Auto-select active services
        <?php
		$admin_services_saved = is_array($_POST['A2A_SHARE_SAVE_active_services']) || isset($_POST['Submit']);
		$active_services = ( $admin_services_saved )
			? $_POST['A2A_SHARE_SAVE_active_services'] : get_option('A2A_SHARE_SAVE_active_services');
		if( !$active_services )
			$active_services = Array();
		$active_services_last = end($active_services);
		$active_services_quoted = '';
		foreach ($active_services as $service) {
			if($admin_services_saved)
				$service = substr($service, 7); // Remove a2a_wp_
			$active_services_quoted .= '"'.$service.'"';
			if ( $service != $active_services_last )
				$active_services_quoted .= ',';
		}
		?>
        var services = [<?php echo $active_services_quoted; ?>];
        jQuery.each(services, function(i, val){
        	jQuery('#a2a_wp_'+val).click();
		});
		
		// Add/Remove Services
		jQuery('#addtoany_services_sortable .dummy:first').after('<li id="addtoany_show_services"><?php _e('Add/Remove Services', 'add-to-any'); ?> &#187;</li>');
		jQuery('#addtoany_show_services').click(function(e){
			jQuery('#addtoany_services_selectable, #addtoany_services_info').slideDown('fast');
			jQuery(this).fadeOut('fast');
		});
		jQuery('#addtoany_show_template_button_code').click(function(e){
			jQuery('#addtoany_template_button_code').slideDown('fast');
			jQuery(this).fadeOut('fast');
		});
		jQuery('#addtoany_show_css_code').click(function(e){
			jQuery('#addtoany_css_code').slideDown('fast');
			jQuery(this).fadeOut('fast');
		});
	});
	--></script>

	<style type="text/css">
	.ui-sortable-placeholder{border:1px dashed #AAA;}
	.addtoany_admin_list{list-style:none;padding:0;margin:0;}
	.addtoany_admin_list li{-webkit-border-radius:9px;-moz-border-radius:9px;border-radius:9px;}
	
	#addtoany_services_selectable{clear:left;display:none;}
	#addtoany_services_selectable li{cursor:crosshair;float:left;width:150px;font-size:11px;margin:0;padding:3px;border:1px solid transparent;_border-color:#FAFAFA/*IE6*/;overflow:hidden;}
	<?php // white-space:nowrap could go above, but then webkit does not wrap floats if parent has no width set; wrapping in <span> instead (below) ?>
	#addtoany_services_selectable li span{white-space:nowrap;}
	#addtoany_services_selectable li:hover, #addtoany_services_selectable li.addtoany_selected{border:1px solid #AAA;background-color:#FFF;}
	#addtoany_services_selectable li.addtoany_selected:hover{border-color:#F00;}
	#addtoany_services_selectable li:active{border:1px solid #000;}
    #addtoany_services_selectable li span img{margin:0 4px 0 4px;width:16px;height:16px;border:0;vertical-align:middle;}
	
	#addtoany_services_sortable li, #addtoany_services_sortable li.dummy:hover{cursor:move;float:left;padding:9px;border:1px solid transparent;_border-color:#FAFAFA/*IE6*/;}
	#addtoany_services_sortable li:hover{border:1px solid #AAA;background-color:#FFF;}
	#addtoany_services_sortable li.dummy, #addtoany_services_sortable li.dummy:hover{cursor:auto;background-color:transparent;}
	#addtoany_services_sortable img{width:16px;height:16px;border:0;vertical-align:middle;}
	
	li#addtoany_show_services{border:1px solid #DFDFDF;background-color:#FFF;cursor:pointer;}
	li#addtoany_show_services:hover{border:1px solid #AAA;}
	#addtoany_services_info{clear:left;display:none;}
	
	#addtoany_template_button_code, #addtoany_css_code{display:none;}
    </style>
<?php
	}
}

add_filter('admin_head', 'A2A_SHARE_SAVE_admin_head');

function A2A_SHARE_SAVE_add_menu_link() {
	global $wp_version;
		
	if( current_user_can('manage_options') ) {
		add_options_page(
			'AddToAny: '. __("Share/Save", "add-to-any"). " " . __("Settings")
			, __("Share/Save Buttons", "add-to-any")
			, 'activate_plugins' 
			, basename(__FILE__)
			, 'A2A_SHARE_SAVE_options_page'
		);
		
		// Load jQuery UI Sortable
		// Must be on WP 2.6+
		if ($wp_version >= "2.6") {
			wp_enqueue_script('jquery-ui-sortable');
		}
	}
}

add_filter('admin_menu', 'A2A_SHARE_SAVE_add_menu_link');

// Place in Settings Option List
function A2A_SHARE_SAVE_actlinks( $links, $file ){
	//Static so we don't call plugin_basename on every plugin row.
	static $this_plugin;
	if ( ! $this_plugin ) $this_plugin = plugin_basename(__FILE__);
	
	if ( $file == $this_plugin ){
		$settings_link = '<a href="options-general.php?page=add-to-any.php">' . __('Settings') . '</a>';
		array_unshift( $links, $settings_link ); // before other links
	}
	return $links;
}

add_filter("plugin_action_links", 'A2A_SHARE_SAVE_actlinks', 10, 2);


?>