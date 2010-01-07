<?php
/*
Plugin Name: Add to Any: Share/Bookmark/Email Button
Plugin URI: http://www.addtoany.com/
Description: Help readers share, bookmark, and email your posts and pages using any service.  [<a href="options-general.php?page=add-to-any.php">Settings</a>]
Version: .9.9.3.5
Author: Add to Any
Author URI: http://www.addtoany.com/contact/
*/

if( !isset($A2A_javascript) )
	$A2A_javascript = '';
if( !isset($A2A_locale) )
	$A2A_locale = '';

// Pre-2.6 compatibility
if ( !defined('WP_CONTENT_URL') )
    define( 'WP_CONTENT_URL', get_option('siteurl') . '/wp-content');
if ( ! defined( 'WP_PLUGIN_URL' ) )
      define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );

$A2A_SHARE_SAVE_plugin_basename = plugin_basename(dirname(__FILE__));
$A2A_SHARE_SAVE_plugin_url_path = WP_PLUGIN_URL.'/'.$A2A_SHARE_SAVE_plugin_basename; // /wp-content/plugins/add-to-any

function A2A_SHARE_SAVE_textdomain() {
	global $A2A_SHARE_SAVE_plugin_url_path, $A2A_SHARE_SAVE_plugin_basename;
	
	load_plugin_textdomain('add-to-any',
		$A2A_SHARE_SAVE_plugin_url_path.'/languages',
		$A2A_SHARE_SAVE_plugin_basename.'/languages');
}
add_action('init', 'A2A_SHARE_SAVE_textdomain');

function A2A_SHARE_SAVE_link_vars() {
	global $post;
	
	$linkname		= get_the_title($post->ID);
	$linkname_enc	= rawurlencode( $linkname );
	$linkurl		= get_permalink($post->ID);
	$linkurl_enc	= rawurlencode( $linkurl );	
	
	return compact( 'linkname', 'linkname_enc', 'linkurl', 'linkurl_enc' );
}

include_once('services.php');

function ADDTOANY_SHARE_SAVE_ICONS( $args = false ) {
	if( $args )
		extract( $args ); // output_later, html_wrap_open, html_wrap_close
	extract(A2A_SHARE_SAVE_link_vars()); // linkname_enc, etc.
		
	global $A2A_SHARE_SAVE_plugin_url_path, $A2A_SHARE_SAVE_services;
	
	$active_services = get_option('A2A_SHARE_SAVE_active_services');
	
	$ind_html = "";
	
	if( !$active_services )
		$active_services = Array();
	
	foreach($active_services as $active_service) {
		
		// Skip unknown
		if( !in_array($active_service, array_keys($A2A_SHARE_SAVE_services)) )
			continue;

		$service = $A2A_SHARE_SAVE_services[$active_service];
		$safe_name = $active_service;
		$name = $service['name'];
		$icon = $service['icon'];
		$url = "http://www.addtoany.com/add_to/" . $safe_name . "?linkurl=" . $linkurl_enc . "&amp;linkname=" . $linkname_enc;
		$link = $html_wrap_open."<a href=\"$url\" title=\"$name\" rel=\"nofollow\" target=\"_blank\">";
		$link .= "<img src=\"".$A2A_SHARE_SAVE_plugin_url_path."/icons/".$icon.".png\" alt=\"$name\"/>";
		$link .= "</a>".$html_wrap_close;
		
		$ind_html .= apply_filters('addtoany_link', $link);
	}
	
	if($output_later)
		return $ind_html;
	else
		echo $ind_html;
}

function ADDTOANY_SHARE_SAVE_BUTTON( $args = false ) {

	global $A2A_SHARE_SAVE_plugin_url_path, $A2A_SHARE_SAVE_services;
	
	if( $args )
		extract( $args ); // output_later, html_wrap_open, html_wrap_close
	
	extract(A2A_SHARE_SAVE_link_vars()); // linkname_enc, etc.
	
	/* Add to Any button */
	
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
	
	$button_html = $html_wrap_open.'<a class="a2a_dd addtoany_share_save" href="http://www.addtoany.com/share_save?'
		.'linkurl='.$linkurl_enc
		.'&amp;linkname='.$linkname_enc
		.'"' . $style . $button_target
		.'>'.$button.'</a>'.$html_wrap_close;
	
	// If not a feed
	if( !is_feed() ) {
	
		global $A2A_javascript, $A2A_SHARE_SAVE_external_script_called;
		if( $A2A_javascript == '' || !$A2A_SHARE_SAVE_external_script_called ) {
			$external_script_call = '</script><script type="text/javascript" src="http://static.addtoany.com/menu/page.js"></script>';
			$A2A_SHARE_SAVE_external_script_called = true;
		}
		else
			$external_script_call = 'a2a_init("page");</script>';
		$A2A_javascript .= '<script type="text/javascript">' . "\n"
			. A2A_menu_locale()
			. 'a2a_linkname="' . js_escape($linkname) . '";' . "\n"
			. 'a2a_linkurl="' . $linkurl . '";' . "\n"
			. ((get_option('A2A_SHARE_SAVE_onclick')=='1') ? 'a2a_onclick=1;' . "\n" : '')
			. ((get_option('A2A_SHARE_SAVE_hide_embeds')=='-1') ? 'a2a_hide_embeds=0;' . "\n" : '')
			. ((get_option('A2A_SHARE_SAVE_show_title')=='1') ? 'a2a_show_title=1;' . "\n" : '')
			. (($A2A_javascript == '' || !$A2A_SHARE_SAVE_external_script_called) ? stripslashes(get_option('A2A_SHARE_SAVE_additional_js_variables')) . "\n" : '')
			. $external_script_call . "\n\n";
		
		remove_action('wp_footer', 'A2A_menu_javascript');
		add_action('wp_footer', 'A2A_menu_javascript');
	
	}
	
	if($output_later)
		return $button_html;
	else
		echo $button_html;
}

if (!function_exists('A2A_menu_javascript')) {
	function A2A_menu_javascript() {
		global $A2A_javascript;
		echo $A2A_javascript;
	}
}

if (!function_exists('A2A_menu_locale')) {
	function A2A_menu_locale() {
		global $A2A_locale;
		if( $A2A_locale != '' ) return false;
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


function A2A_SHARE_SAVE_to_bottom_of_content($content) {
	$is_feed = is_feed();
	
	if ( 
		( 
			// Tags
			strpos($content, '<!--sharesave-->')===false || 											// <!--sharesave--> tag
			strpos($content, '<!--nosharesave-->')!==false												// <!--nosharesave--> tag
		) &&											
		(
			// Posts
			( ! is_page() && get_option('A2A_SHARE_SAVE_display_in_posts')=='-1' ) || 					// All posts
			( is_home() && get_option('A2A_SHARE_SAVE_display_in_posts_on_front_page')=='-1' ) ||  		// Front page posts
			( is_category() && get_option('A2A_SHARE_SAVE_display_in_posts_on_front_page')=='-1' ) ||  	// Category posts (same as Front page option)
			( is_tag() && get_option('A2A_SHARE_SAVE_display_in_posts_on_front_page')=='-1' ) ||  		// Tag Cloud posts (same as Front page option)
			( is_date() && get_option('A2A_SHARE_SAVE_display_in_posts_on_front_page')=='-1' ) ||  		// Date-based archives posts (same as Front page option)
			( is_author() && get_option('A2A_SHARE_SAVE_display_in_posts_on_front_page')=='-1' ) ||  	// Author posts (same as Front page option)
			( is_search() && get_option('A2A_SHARE_SAVE_display_in_posts_on_front_page')=='-1' ) ||  	// Search results posts (same as Front page option)
			( $is_feed && (get_option('A2A_SHARE_SAVE_display_in_feed')=='-1' ) || 					// Posts in feed
			
			// Pages
			( is_page() && get_option('A2A_SHARE_SAVE_display_in_pages')=='-1' ) ||						// Individual pages
			( (strpos($content, '<!--nosharesave-->')!==false) )										// <!--nosharesave-->
		)
		)
	)	
		return $content;
	
	$icons_args = array(
		"output_later" => true,
		"html_wrap_open" => ($is_feed) ? "" : "<li>",
		"html_wrap_close" => ($is_feed) ? " " : "</li>",
	);
	
	$A2A_SHARE_SAVE_options = array(
		"output_later" => true,
		"html_wrap_open" => ($is_feed) ? "" : "<li>",
		"html_wrap_close" => ($is_feed) ? "" : "</li>",
	);
	
	if ( ! $is_feed ) {
		$container_wrap_open = '<div class="addtoany_share_save_container"><ul class="addtoany_list">';
		$container_wrap_close = '</ul></div>';
	}
	
	$content .= $container_wrap_open.ADDTOANY_SHARE_SAVE_ICONS( $icons_args ).ADDTOANY_SHARE_SAVE_BUTTON( $A2A_SHARE_SAVE_options ).$container_wrap_close;
	return $content;
}

add_action('the_content', 'A2A_SHARE_SAVE_to_bottom_of_content', 98);


function A2A_SHARE_SAVE_button_css() {
	?><style type="text/css">
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
		line-height:32px;<?php /* For vertical space in the event of wrapping*/ ?>
		list-style-type:none;
		margin:0 !important;
		padding:0 !important;
	}
	ul.addtoany_list li:before{content:"";}
	ul.addtoany_list li a{padding:0 9px;}
	ul.addtoany_list img{
		float:none;
		width:16px;
		height:16px;
		border:0;
		margin:0;
		padding:0;
		vertical-align:middle;
	}
	ul.addtoany_list a img{
		opacity:.6;
		-moz-opacity:.6;
		filter:alpha(opacity=60);
	}
	ul.addtoany_list a:hover img, ul.addtoany_list a.addtoany_share_save img{
		opacity:1;
		-moz-opacity:1;
		filter:alpha(opacity=100);
	}
	a.addtoany_share_save img{border:0;width:auto;height:auto;}<?php /* Must declare after "ul.addtoany_list img" */ ?>
    </style>
<?php
}

add_action('wp_head', 'A2A_SHARE_SAVE_button_css');




/*****************************
		OPTIONS
******************************/


function A2A_SHARE_SAVE_options_page() {

	global $A2A_SHARE_SAVE_plugin_url_path, $A2A_SHARE_SAVE_services;

    if( $_POST[ 'A2A_SHARE_SAVE_submit_hidden' ] == 'Y' ) {

        update_option( 'A2A_SHARE_SAVE_display_in_posts_on_front_page', ($_POST['A2A_SHARE_SAVE_display_in_posts_on_front_page']=='1') ? '1':'-1' );
		update_option( 'A2A_SHARE_SAVE_display_in_posts', ($_POST['A2A_SHARE_SAVE_display_in_posts']=='1') ? '1':'-1' );
		update_option( 'A2A_SHARE_SAVE_display_in_pages', ($_POST['A2A_SHARE_SAVE_display_in_pages']=='1') ? '1':'-1' );
		update_option( 'A2A_SHARE_SAVE_display_in_feed', ($_POST['A2A_SHARE_SAVE_display_in_feed']=='1') ? '1':'-1' );
		update_option( 'A2A_SHARE_SAVE_hide_embeds', ($_POST['A2A_SHARE_SAVE_hide_embeds']=='1') ? '1':'-1' );
		update_option( 'A2A_SHARE_SAVE_show_title', ($_POST['A2A_SHARE_SAVE_show_title']=='1') ? '1':'-1' );
		update_option( 'A2A_SHARE_SAVE_onclick', ($_POST['A2A_SHARE_SAVE_onclick']=='1') ? '1':'-1' );
		update_option( 'A2A_SHARE_SAVE_button_opens_new_window', ($_POST['A2A_SHARE_SAVE_button_opens_new_window']=='1') ? '1':'-1' );
		update_option( 'A2A_SHARE_SAVE_button', $_POST['A2A_SHARE_SAVE_button'] );
		update_option( 'A2A_SHARE_SAVE_button_custom', $_POST['A2A_SHARE_SAVE_button_custom'] );
		update_option( 'A2A_SHARE_SAVE_additional_js_variables', trim($_POST['A2A_SHARE_SAVE_additional_js_variables']) );
		
		// Store desired text if 16 x 16px buttons or text-only is chosen:
		if( get_option('A2A_SHARE_SAVE_button') == 'favicon.png|16|16' )
			update_option( 'A2A_SHARE_SAVE_button_text', $_POST['A2A_SHARE_SAVE_button_favicon_16_16_text'] );
		elseif( get_option('A2A_SHARE_SAVE_button') == 'share_16_16.png|16|16' )
			update_option( 'A2A_SHARE_SAVE_button_text', $_POST['A2A_SHARE_SAVE_button_share_16_16_text'] );
		else
			update_option( 'A2A_SHARE_SAVE_button_text', ( trim($_POST['A2A_SHARE_SAVE_button_text']) != '' ) ? $_POST['A2A_SHARE_SAVE_button_text'] : "Share/Save" );
			
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

	<h2><?php _e( 'Add to Any: Share/Save ', 'add-to-any' ) . _e( 'Settings' ); ?></h2>

    <form method="post" action="">
    
	<?php wp_nonce_field('update-options'); ?>
    
    	<input type="hidden" name="A2A_SHARE_SAVE_submit_hidden" value="Y">
    
        <table class="form-table">
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
					
                    foreach ($A2A_SHARE_SAVE_services as $service_safe_name=>$site) { ?>
                        <li id="a2a_wp_<?php echo $service_safe_name; ?>"
                            title="<?php echo $site['name']; ?>">
                            <span><img src="<?php echo $A2A_SHARE_SAVE_plugin_url_path.'/icons/'.$site['icon']; ?>.png" width="16" height="16" alt="" /><?php echo $site['name']; ?></span>
                        </li>
				<?php
                    } ?>
                </ul>
            </fieldset></td>
            </tr>
        	<tr valign="top">
            <th scope="row"><?php _e("Button", "add-to-any"); ?></th>
            <td><fieldset>
            	<label>
                	<input name="A2A_SHARE_SAVE_button" value="favicon.png|16|16" type="radio"<?php if(get_option('A2A_SHARE_SAVE_button')=='favicon.png|16|16') echo ' checked="checked"'; ?>
                    	 style="margin:9px 0;vertical-align:middle">
                    <img src="<?php echo $A2A_SHARE_SAVE_plugin_url_path.'/favicon.png'; ?>" width="16" height="16" border="0" style="padding:9px;vertical-align:middle" alt="+ Share/Save" title="+ Share/Save"
                    	onclick="this.parentNode.firstChild.checked=true"/>
                </label>
                <input name="A2A_SHARE_SAVE_button_favicon_16_16_text" type="text" class="code" size="50" onclick="e=document.getElementsByName('A2A_SHARE_SAVE_button');e[e.length-7].checked=true" style="vertical-align:middle;width:150px"
                	value="<?php echo (get_option('A2A_SHARE_SAVE_button_text') !== FALSE) ? stripslashes(get_option('A2A_SHARE_SAVE_button_text')) : "Share/Save"; ?>" />
                <label style="padding-left:9px">
                	<input name="A2A_SHARE_SAVE_button" value="share_16_16.png|16|16" type="radio"<?php if(get_option('A2A_SHARE_SAVE_button')=='share_16_16.png|16|16') echo ' checked="checked"'; ?>
                    	 style="margin:9px 0;vertical-align:middle">
                    <img src="<?php echo $A2A_SHARE_SAVE_plugin_url_path.'/share_16_16.png'; ?>" width="16" height="16" border="0" style="padding:9px;vertical-align:middle" alt="+ Share/Save" title="+ Share/Save"
                    	onclick="this.parentNode.firstChild.checked=true"/>
                </label>
                <input name="A2A_SHARE_SAVE_button_share_16_16_text" type="text" class="code" size="50" onclick="e=document.getElementsByName('A2A_SHARE_SAVE_button');e[e.length-6].checked=true" style="vertical-align:middle;width:150px"
                	value="<?php echo (get_option('A2A_SHARE_SAVE_button_text') !== FALSE) ? stripslashes(get_option('A2A_SHARE_SAVE_button_text')) : "Share/Save"; ?>" /><br>
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
                	value="<?php echo ( trim(get_option('A2A_SHARE_SAVE_button_text')) != '' ) ? stripslashes(get_option('A2A_SHARE_SAVE_button_text')) : "Share/Save"; ?>" />
                
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
                      <code>&lt;?php echo '&lt;ul class=&quot;addtoany_list&quot;&gt;';  if( function_exists('ADDTOANY_SHARE_SAVE_ICONS') )      ADDTOANY_SHARE_SAVE_ICONS( array(&quot;html_wrap_open&quot; =&gt; &quot;&lt;li&gt;&quot;, &quot;html_wrap_close&quot; =&gt; &quot;&lt;/li&gt;&quot;) );  if( function_exists('ADDTOANY_SHARE_SAVE_BUTTON') )      ADDTOANY_SHARE_SAVE_BUTTON( array(&quot;html_wrap_open&quot; =&gt; &quot;&lt;li&gt;&quot;, &quot;html_wrap_close&quot; =&gt; &quot;&lt;/li&gt;&quot;) );  echo '&lt;/ul&gt;'; ?&gt;</code>
                    </div>
                    <noscript<code>&lt;?php echo '&lt;ul class=&quot;addtoany_list&quot;&gt;';  if( function_exists('ADDTOANY_SHARE_SAVE_ICONS') )      ADDTOANY_SHARE_SAVE_ICONS( array(&quot;html_wrap_open&quot; =&gt; &quot;&lt;li&gt;&quot;, &quot;html_wrap_close&quot; =&gt; &quot;&lt;/li&gt;&quot;) );  if( function_exists('ADDTOANY_SHARE_SAVE_BUTTON') )      ADDTOANY_SHARE_SAVE_BUTTON( array(&quot;html_wrap_open&quot; =&gt; &quot;&lt;li&gt;&quot;, &quot;html_wrap_close&quot; =&gt; &quot;&lt;/li&gt;&quot;) );  echo '&lt;/ul&gt;'; ?&gt;</code></noscript>
                </div>
            </fieldset></td>
            </tr>
            <tr valign="top">
            <th scope="row"><?php _e('Menu Style', 'add-to-any'); ?></th>
            <td><fieldset>
					<p><?php _e("Using Add to Any's Menu Styler, you can customize the colors of your Share/Save menu! When you're done, be sure to paste the generated code in the <a href=\"#\" onclick=\"document.getElementById('A2A_SHARE_SAVE_additional_js_variables').focus();return false\">Additional Options</a> box below.", "add-to-any"); ?></p>
                    <p>
                		<a href="http://www.addtoany.com/buttons/share_save/menu_style/wordpress" class="button-secondary" title="<?php _e("Open the Add to Any Menu Styler in a new window", "add-to-any"); ?>" target="_blank"
                        	onclick="document.getElementById('A2A_SHARE_SAVE_additional_js_variables').focus();
                            	document.getElementById('A2A_SHARE_SAVE_menu_styler_note').style.display='';"><?php _e("Open Menu Styler", "add-to-any"); ?></a>
					</p>
            </fieldset></td>
            </tr>
            <tr valign="top">
            <th scope="row"><?php _e('Menu Options', 'add-to-any'); ?></th>
            <td><fieldset>
            	<label>
                	<input name="A2A_SHARE_SAVE_hide_embeds" 
                        type="checkbox"<?php if(get_option('A2A_SHARE_SAVE_hide_embeds')!='-1') echo ' checked="checked"'; ?> value="1"/>
                	<?php _e('Hide embedded objects (Flash, video, etc.) that intersect with the menu when displayed', 'add-to-any'); ?>
                </label><br />
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
                            <strong><?php _e("Paste the code from Add to Any's Menu Styler in the box below!", 'add-to-any'); ?></strong>
                        </label>
                    </p>
                    <label for="A2A_SHARE_SAVE_additional_js_variables">
                    	<p><?php _e('Below you can set special JavaScript variables to apply to each Share/Save menu.', 'add-to-any'); ?>
                    	<?php _e("Advanced users might want to explore Add to Any's <a href=\"http://www.addtoany.com/buttons/api/\" target=\"_blank\">JavaScript API</a>.", "add-to-any"); ?></p>
					</label>
                    <p>
                		<textarea name="A2A_SHARE_SAVE_additional_js_variables" id="A2A_SHARE_SAVE_additional_js_variables" class="code" style="width: 98%; font-size: 12px;" rows="5" cols="50"><?php echo stripslashes(get_option('A2A_SHARE_SAVE_additional_js_variables')); ?></textarea>
					</p>
                    <?php if( get_option('A2A_SHARE_SAVE_additional_js_variables')!='' ) { ?>
                    <label for="A2A_SHARE_SAVE_additional_js_variables" class="setting-description"><?php _e("<strong>Note</strong>: If you're adding new code, be careful not to accidentally overwrite any previous code.</label>", 'add-to-any'); ?>
                    <?php } ?>
            </fieldset></td>
            </tr>
        </table>
        
        <p class="submit">
            <input class="button-primary" type="submit" name="Submit" value="<?php _e('Save Changes', 'add-to-any' ) ?>" />
        </p>
    
    </form>
    
    <h2><?php _e('Like this plugin?','add-to-any'); ?></h2>
    <p><?php _e('<a href="http://wordpress.org/extend/plugins/add-to-any/">Give it a good rating</a> on WordPress.org.','add-to-any'); ?></p>
    <p><?php _e('<a href="http://www.addtoany.com/share_save?linkname=WordPress%20Share%20Plugin%20by%20AddToAny.com&amp;linkurl=http%3A%2F%2Fwordpress.org%2Fextend%2Fplugins%2Fadd-to-any%2F">Share it</a> with your friends.','add-to-any'); ?></p>
    
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
		$admin_services_saved = is_array($_POST['A2A_SHARE_SAVE_active_services']) || isset($_POST['A2A_SHARE_SAVE_submit_hidden']);
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
	
	#addtoany_template_button_code{display:none;}
    </style>
<?php
	}
}

add_action('admin_head', 'A2A_SHARE_SAVE_admin_head');

function A2A_SHARE_SAVE_add_menu_link() {
	if( current_user_can('manage_options') ) {
		add_options_page(
			'Add to Any: '. __("Share/Save", "add-to-any"). " " . __("Settings")
			, __("Share/Save Buttons", "add-to-any")
			, 8 
			, basename(__FILE__)
			, 'A2A_SHARE_SAVE_options_page'
		);
		
		// Load jQuery UI Sortable
		wp_enqueue_script('jquery-ui-sortable');
	}
}

add_action('admin_menu', 'A2A_SHARE_SAVE_add_menu_link');

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