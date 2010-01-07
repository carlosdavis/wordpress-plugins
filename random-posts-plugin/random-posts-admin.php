<?php

// Admin stuff for Random Posts Plugin, Version 2.6.2.0

function random_posts_option_menu() {
	add_options_page(__('Random Posts Options', 'random_posts_plugin'), __('Random Posts', 'random_posts_plugin'), 8, 'random-posts-plugin', 'random_posts_options_page');
}

add_action('admin_menu', 'random_posts_option_menu', 1);

function random_posts_options_page(){
	echo '<div class="wrap"><h2>';
	_e('Random Posts ', 'random_posts_plugin'); 
	echo '<a href="http://rmarsh.com/plugins/post-options/" style="font-size: 0.8em;">';
	_e('help and instructions'); 
	echo '</a></h2></div>';
	if (!RandomPosts::check_post_plugin_library(__('<h1>Please install the <a href="http://downloads.wordpress.org/plugin/post-plugin-library.zip">Post Plugin Library</a> plugin.</h1>'))) return;
	$m = new admin_subpages();
	$m->add_subpage('General', 'general', 'random_posts_general_options_subpage');
	$m->add_subpage('Output', 'output', 'random_posts_output_options_subpage');
	$m->add_subpage('Filter', 'filter', 'random_posts_filter_options_subpage');
	$m->add_subpage('Placement', 'placement', 'random_posts_placement_options_subpage');
	$m->add_subpage('Other', 'other', 'random_posts_other_options_subpage');
	$m->add_subpage('Report a Bug', 'bug', 'random_posts_bug_subpage');
	$m->add_subpage('Remove this Plugin', 'remove', 'random_posts_remove_subpage');
	$m->display();
	add_action('in_admin_footer', 'random_posts_admin_footer');
}

function random_posts_admin_footer() {
	ppl_admin_footer(str_replace('-admin', '', __FILE__), "random-posts");
}

function random_posts_general_options_subpage(){
	global $wpdb, $wp_version;
	$options = get_option('random-posts');
	if (isset($_POST['update_options'])) {
		check_admin_referer('random-posts-update-options'); 
		if (defined('POC_CACHE_4')) poc_cache_flush();
		// Fill up the options with the values chosen...
		$options = ppl_options_from_post($options, array('limit', 'skip', 'show_private', 'show_pages', 'show_attachments', 'status', 'age', 'omit_current_post', 'match_cat', 'match_tags', 'match_author'));
		update_option('random-posts', $options);
		// Show a message to say we've done something
		echo '<div class="updated fade"><p>' . __('Options saved', 'random_posts_plugin') . '</p></div>';
	} 
	//now we drop into html to display the option page form
	?>
		<div class="wrap">
		<h2><?php _e('General Settings', 'random_posts_plugin'); ?></h2>
		<form method="post" action="">
		<div class="submit"><input type="submit" name="update_options" value="<?php _e('Save General Settings', 'random_posts_plugin') ?>" /></div>
		<table class="optiontable form-table">
			<?php 
				ppl_display_limit($options['limit']); 
				ppl_display_skip($options['skip']); 
				ppl_display_show_private($options['show_private']); 
				ppl_display_show_pages($options['show_pages']); 
				ppl_display_show_attachments($options['show_attachments']); 
				ppl_display_status($options['status']);
				ppl_display_age($options['age']);
				ppl_display_omit_current_post($options['omit_current_post']); 
				ppl_display_match_cat($options['match_cat']); 
				ppl_display_match_tags($options['match_tags']); 
				ppl_display_match_author($options['match_author']); 
			?>
		</table>
		<div class="submit"><input type="submit" name="update_options" value="<?php _e('Save General Settings', 'random_posts_plugin') ?>" /></div>
		<?php if (function_exists('wp_nonce_field')) wp_nonce_field('random-posts-update-options'); ?>
		</form>  
	</div>
	<?php	
}

function random_posts_output_options_subpage(){
	global $wpdb, $wp_version;
	$options = get_option('random-posts');
	if (isset($_POST['update_options'])) {
		check_admin_referer('random-posts-update-options'); 
		if (defined('POC_CACHE_4')) poc_cache_flush();
		// Fill up the options with the values chosen...
		$options = ppl_options_from_post($options, array('output_template', 'prefix', 'suffix', 'none_text', 'no_text', 'divider', 'sort', 'group_template'));
		update_option('random-posts', $options);
		// Show a message to say we've done something
		echo '<div class="updated fade"><p>' . __('Options saved', 'random_posts_plugin') . '</p></div>';
	} 
	//now we drop into html to display the option page form
	?>
		<div class="wrap">
		<h2><?php _e('Output Settings', 'random_posts_plugin'); ?></h2>
		<form method="post" action="">
		<div class="submit"><input type="submit" name="update_options" value="<?php _e('Save Output Settings', 'random_posts_plugin') ?>" /></div>
		<table class="optiontable form-table">
			<tr>
			<td>
			<table>
			<?php 
				ppl_display_output_template($options['output_template']); 
				ppl_display_prefix($options['prefix']); 
				ppl_display_suffix($options['suffix']); 
				ppl_display_none_text($options['none_text']); 
				ppl_display_no_text($options['no_text']); 
				ppl_display_divider($options['divider']); 
				ppl_display_sort($options['sort']);
				ppl_display_group_template($options['group_template']); 
			?>
			</table>
			</td>
			<td>
			<?php ppl_display_available_tags('random-posts'); ?>
			</td></tr>
		</table>
		<div class="submit"><input type="submit" name="update_options" value="<?php _e('Save Output Settings', 'random_posts_plugin') ?>" /></div>
		<?php if (function_exists('wp_nonce_field')) wp_nonce_field('random-posts-update-options'); ?>
		</form>  
	</div>
	<?php	
}

function random_posts_filter_options_subpage(){
	global $wpdb, $wp_version;
	$options = get_option('random-posts');
	if (isset($_POST['update_options'])) {
		check_admin_referer('random-posts-update-options'); 
		if (defined('POC_CACHE_4')) poc_cache_flush();
		// Fill up the options with the values chosen...
		$options = ppl_options_from_post($options, array('excluded_posts', 'included_posts', 'excluded_authors', 'included_authors', 'excluded_cats', 'included_cats', 'tag_str', 'custom'));
		update_option('random-posts', $options);
		// Show a message to say we've done something
		echo '<div class="updated fade"><p>' . __('Options saved', 'random_posts_plugin') . '</p></div>';
	} 
	//now we drop into html to display the option page form
	?>
		<div class="wrap">
		<h2><?php _e('Filter Settings', 'random_posts_plugin'); ?></h2>
		<form method="post" action="">
		<div class="submit"><input type="submit" name="update_options" value="<?php _e('Save Filter Settings', 'random_posts_plugin') ?>"  /></div>
		<table class="optiontable form-table">
			<?php 
				ppl_display_excluded_posts($options['excluded_posts']); 
				ppl_display_included_posts($options['included_posts']); 
				ppl_display_authors($options['excluded_authors'], $options['included_authors']); 
				ppl_display_cats($options['excluded_cats'], $options['included_cats']); 
				ppl_display_tag_str($options['tag_str']); 
				ppl_display_custom($options['custom']); 
			?>
		</table>
		<div class="submit"><input type="submit" name="update_options" value="<?php _e('Save Filter Settings', 'random_posts_plugin') ?>"  /></div>
		<?php if (function_exists('wp_nonce_field')) wp_nonce_field('random-posts-update-options'); ?>
		</form>  
	</div>
	<?php	
}

function random_posts_placement_options_subpage(){
	global $wpdb, $wp_version;
	$options = get_option('random-posts');
	if (isset($_POST['update_options'])) {
		check_admin_referer('random-posts-update-options'); 
		if (defined('POC_CACHE_4')) poc_cache_flush();
		// Fill up the options with the values chosen...
		$options = ppl_options_from_post($options, array('content_filter', 'widget_parameters', 'widget_condition', 'feed_on', 'feed_priority', 'feed_parameters', 'append_on', 'append_priority', 'append_parameters', 'append_condition'));
		update_option('random-posts', $options);
		// Show a message to say we've done something
		echo '<div class="updated fade"><p>' . __('Options saved', 'random_posts') . '</p></div>';
	} 
	//now we drop into html to display the option page form
	?>
		<div class="wrap">
		<h2><?php _e('Placement Settings', 'random_posts'); ?></h2>
		<form method="post" action="">
		<div class="submit"><input type="submit" name="update_options" value="<?php _e('Save Placement Settings', 'random_posts') ?>" /></div>
		<table class="optiontable form-table">
			<?php 
				ppl_display_append($options); 
				ppl_display_feed($options); 
				ppl_display_widget($options); 
				ppl_display_content_filter($options['content_filter']);
			?>
		</table>
		<div class="submit"><input type="submit" name="update_options" value="<?php _e('Save Placement Settings', 'random_posts') ?>" /></div>
		<?php if (function_exists('wp_nonce_field')) wp_nonce_field('random-posts-update-options'); ?>
		</form>  
	</div>
	<?php	
}

function random_posts_other_options_subpage(){
	global $wpdb, $wp_version;
	$options = get_option('random-posts');
	if (isset($_POST['update_options'])) {
		check_admin_referer('random-posts-update-options'); 
		if (defined('POC_CACHE_4')) poc_cache_flush();
		// Fill up the options with the values chosen...
		$options = ppl_options_from_post($options, array('stripcodes'));
		update_option('random-posts', $options);
		// Show a message to say we've done something
		echo '<div class="updated fade"><p>' . __('Options saved', 'random_posts_plugin') . '</p></div>';
	} 
	//now we drop into html to display the option page form
	?>
		<div class="wrap">
		<h2><?php _e('Other Settings', 'random_posts_plugin'); ?></h2>
		<form method="post" action="">
		<div class="submit"><input type="submit" name="update_options" value="<?php _e('Save Other Settings', 'random_posts_plugin') ?>"  /></div>
		<table class="optiontable form-table">
			<?php 
				ppl_display_stripcodes($options['stripcodes']); 
			?>
		</table>
		<div class="submit"><input type="submit" name="update_options" value="<?php _e('Save Other Settings', 'random_posts_plugin') ?>"  /></div>
		<?php if (function_exists('wp_nonce_field')) wp_nonce_field('random-posts-update-options'); ?>
		</form>  
	</div>
	<?php	
}

function random_posts_bug_subpage(){
	ppl_bug_form('random-posts'); 
}

function random_posts_remove_subpage(){
	ppl_plugin_eradicate_form(str_replace('-admin', '', __FILE__)); 
}	
		
function random_posts_install() {
	$options = get_option('random-posts');
	// check each of the option values and, if empty, assign a default (doing it this long way
	// lets us add new options in later versions)
	if (!isset($options['widget_condition'])) $options['widget_condition'] = '';
	if (!isset($options['widget_parameters'])) $options['widget_parameters'] = '';
	if (!isset($options['feed_on'])) $options['feed_on'] = 'false';
	if (!isset($options['feed_priority'])) $options['feed_priority'] = '10';
	if (!isset($options['feed_parameters'])) $options['feed_parameters'] = 'prefix=<strong>'.__('Random Posts', 'random-posts').':</strong><ul class="random-posts">&suffix=</ul>';
	if (!isset($options['append_on'])) $options['append_on'] = 'false';
	if (!isset($options['append_priority'])) $options['append_priority'] = '10';
	if (!isset($options['append_parameters'])) $options['append_parameters'] = 'prefix=<h3>'.__('Random Posts', 'random-posts').':</h3><ul class="random-posts">&suffix=</ul>';
	if (!isset($options['append_condition'])) $options['append_condition'] = 'is_single()';
	if (!isset($options['limit'])) $options['limit'] = 5;
	if (!isset($options['skip'])) $options['skip'] = 0;
	if (!isset($options['age'])) {$options['age']['direction'] = 'none'; $options['age']['length'] = '0'; $options['age']['duration'] = 'month';}
	if (!isset($options['divider'])) $options['divider'] = '';
	if (!isset($options['omit_current_post'])) $options['omit_current_post'] = 'false';
	if (!isset($options['show_private'])) $options['show_private'] = 'false';
	if (!isset($options['show_pages'])) $options['show_pages'] = 'false';
	if (!isset($options['show_attachments'])) $options['show_attachments'] = 'false';
	// show_static is now show_pages
	if ( isset($options['show_static'])) {$options['show_pages'] = $options['show_static']; unset($options['show_static']);};
	if (!isset($options['none_text'])) $options['none_text'] = __('None Found', 'random_posts_plugin');
	if (!isset($options['no_text'])) $options['no_text'] = 'false';
	if (!isset($options['tag_str'])) $options['tag_str'] = '';
	if (!isset($options['excluded_cats'])) $options['excluded_cats'] = '';
	if ($options['excluded_cats'] === '9999') $options['excluded_cats'] = '';
	if (!isset($options['included_cats'])) $options['included_cats'] = '';
	if ($options['included_cats'] === '9999') $options['included_cats'] = '';
	if (!isset($options['excluded_authors'])) $options['excluded_authors'] = '';
	if ($options['excluded_authors'] === '9999') $options['excluded_authors'] = '';
	if (!isset($options['included_authors'])) $options['included_authors'] = '';
	if ($options['included_authors'] === '9999') $options['included_authors'] = '';
	if (!isset($options['included_posts'])) $options['included_posts'] = '';
	if (!isset($options['excluded_posts'])) $options['excluded_posts'] = '';
	if ($options['excluded_posts'] === '9999') $options['excluded_posts'] = '';
	if (!isset($options['stripcodes'])) $options['stripcodes'] = array(array());
	if (!isset($options['prefix'])) $options['prefix'] = '<ul>';
	if (!isset($options['suffix'])) $options['suffix'] = '</ul>';
	if (!isset($options['output_template'])) $options['output_template'] = '<li>{link}</li>';
	if (!isset($options['match_cat'])) $options['match_cat'] = 'false';
	if (!isset($options['match_tags'])) $options['match_tags'] = 'false';
	if (!isset($options['match_author'])) $options['match_author'] = 'false';
	if (!isset($options['content_filter'])) $options['content_filter'] = 'false';
	if (!isset($options['custom'])) {$options['custom']['key'] = ''; $options['custom']['op'] = '='; $options['custom']['value'] = '';}
	if (!isset($options['sort'])) {$options['sort']['by1'] = ''; $options['sort']['order1'] = SORT_ASC; $options['sort']['case1'] = 'false';$options['sort']['by2'] = ''; $options['sort']['order2'] = SORT_ASC; $options['sort']['case2'] = 'false';}
	if (!isset($options['status'])) {$options['status']['publish'] = 'true'; $options['status']['private'] = 'false'; $options['status']['draft'] = 'false'; $options['status']['future'] = 'false';}
	if (!isset($options['group_template'])) $options['group_template'] = '';
	update_option('random-posts', $options);
}

if (!function_exists('ppl_plugin_basename')) {
	if ( !defined('WP_PLUGIN_DIR') ) define( 'WP_PLUGIN_DIR', ABSPATH . 'wp-content/plugins' ); 
	function ppl_plugin_basename($file) {
		$file = str_replace('\\','/',$file); // sanitize for Win32 installs
		$file = preg_replace('|/+|','/', $file); // remove any duplicate slash
		$plugin_dir = str_replace('\\','/',WP_PLUGIN_DIR); // sanitize for Win32 installs
		$plugin_dir = preg_replace('|/+|','/', $plugin_dir); // remove any duplicate slash
		$file = preg_replace('|^' . preg_quote($plugin_dir, '|') . '/|','',$file); // get relative path from plugins dir
		return $file;
	}
}

add_action('activate_'.str_replace('-admin', '', ppl_plugin_basename(__FILE__)), 'random_posts_install');

?>