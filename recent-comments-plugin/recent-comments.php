<?php
/*
Plugin Name: Recent Comments
Plugin URI: http://rmarsh.com/plugins/recent-comments/
Description: Displays a <a href="options-general.php?page=recent-comments.php">highly configurable</a> list of the most recent comments. <a href="http://rmarsh.com/plugins/post-options/">Instructions and help online</a>. Requires the latest version of the <a href="http://wordpress.org/extend/plugins/post-plugin-library/">Post-Plugin Library</a> to be installed.
Version: 2.6.2.1
Author: Rob Marsh, SJ
Author URI: http://rmarsh.com/
*/

/*
Copyright 2008  Rob Marsh, SJ  (http://rmarsh.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details: http://www.gnu.org/licenses/gpl.txt
*/

$recent_comments_version = '2.6.2.1';

/*
	Template Tag: Displays the most recently commented posts.
		e.g.: <?php recent_comments(); ?>
	Full help and instructions at http://rmarsh.com/plugins/post-options/
*/
function recent_comments($args = '') {
	echo RecentComments::execute($args);
}

function recent_comments_mark_current(){
	global $post, $recent_comments_current_ID;
	$recent_comments_current_ID = $post->ID;
}

/*

	'innards'
	
*/

if (!defined('DSEP')) define('DSEP', DIRECTORY_SEPARATOR);
if (!defined('POST_PLUGIN_LIBRARY')) RecentComments::install_post_plugin_library();

$recent_comments_current_ID = -1;

class RecentComments {

	function execute($args='', $default_output_template='<li>{commentlink}</li>'){
		if (!RecentComments::check_post_plugin_library(__('Post-Plugin Library missing', 'recent_comments_plugin'))) return;
		global $wpdb, $wp_version, $recent_comments_current_ID;
		$start_time = ppl_microtime();
		if (defined('POC_CACHE_4')) {
			$cache_key = 'recent-comments'.$args;
			$result = poc_cache_fetch($cache_key);
			if ($result !== false) return $result . sprintf("<!-- Recent Comments took %.3f ms (cached) -->", 1000 * (ppl_microtime() - $start_time));
		}
		// First we process any arguments to see if any defaults have been overridden
		$options = ppl_parse_args($args);
		// Next we retrieve the stored options and use them unless a value has been overridden via the arguments
		$options = ppl_set_options('recent-comments', $options, $default_output_template);
		if (0 < $options['limit']) {	
			$match_tags = ($options['match_tags'] !== 'false' && $wp_version >= 2.3);
			$exclude_cats = ($options['excluded_cats'] !== '');
			$include_cats = ($options['included_cats'] !== '');
			$exclude_authors = ($options['excluded_authors'] !== '');
			$include_authors = ($options['included_authors'] !== '');
			$exclude_posts = (trim($options['excluded_posts']) !== '');
			$include_posts = (trim($options['included_posts']) !== '');
			$match_category = ($options['match_cat'] === 'true');
			$match_author = ($options['match_author'] === 'true');
			$use_tag_str = ('' != $options['tag_str'] && $wp_version >= 2.3);
			$omit_current_post = ($options['omit_current_post'] !== 'false');
			$just_current_post = ($options['just_current_post'] !== 'false');
			$hide_pass = ($options['show_private'] === 'false');
			$check_age = ('none' !== $options['age']['direction']);
			$check_custom = (trim($options['custom']['key']) !== '');
			$limit = $options['skip'].', '.$options['limit'];
			$unique = $options['unique'] === 'true';
			$filter_comments = ($options['show_type'] === 'comments' OR $options['show_type'] === 'trackbacks');
			$no_author_comments = ($options['no_author_comments'] === 'true'); 
			$no_user_comments = ($options['no_user_comments'] !== 'false');
			
			//the workhorse...
	 		$where = $groupby = array();
			if (!$unique) {
				$sql = "SELECT * FROM $wpdb->comments LEFT JOIN `$wpdb->posts` ON `comment_post_ID` = `ID` ";
			} else {
				$sql = "SELECT *, MAX(comment_date) AS comment_date FROM $wpdb->comments LEFT JOIN `$wpdb->posts` ON `comment_post_ID` = `ID` ";
				$groupby[] = "comment_post_ID";
			}
		    if ($check_custom) {
				$sql .= "LEFT JOIN $wpdb->postmeta ON post_id = ID ";
				$groupby[] = "ID";
			}	
			// build the 'WHERE' clause
			if (!function_exists('get_post_type')) { 
				$where[] = where_hide_future();
			} else {
				$where[] = where_show_status($options['status'], $options['show_attachments']);
			}
			if ($match_category) $where[] = where_match_category();
			if ($match_tags) $where[] = where_match_tags($options['match_tags']);
			if ($match_author) $where[] = where_match_author();
			$where[] = where_show_pages($options['show_pages'], $options['show_attachments']);	
			if ($include_cats) $where[] = where_included_cats($options['included_cats']);
			if ($exclude_cats) $where[] = where_excluded_cats($options['excluded_cats']);
			if ($exclude_authors) $where[] = where_excluded_authors($options['excluded_authors']);
			if ($include_authors) $where[] = where_included_authors($options['included_authors']);
			if ($exclude_posts) $where[] = where_excluded_posts(trim($options['excluded_posts']));
			if ($include_posts) $where[] = where_included_posts(trim($options['included_posts']));
			if ($use_tag_str) $where[] = where_tag_str($options['tag_str']);
			if ($omit_current_post) $where[] = where_omit_post($recent_comments_current_ID);
			if ($just_current_post) $where[] = where_just_post();
			if ($hide_pass) $where[] = where_hide_pass();
			if ($check_age) $where[] = where_check_age($options['age']['direction'], $options['age']['length'], $options['age']['duration']);
			if ($no_author_comments) $where[] = where_author_comments();
			if ($no_user_comments) $where[] = where_user_comments();
			if ($filter_comments) $where[] = where_comment_type($options['show_type']);
			if ($check_custom) $where[] = where_check_custom($options['custom']['key'], $options['custom']['op'], $options['custom']['value']);
			$where[] = "comment_approved = '1'";
			$sql .= "WHERE ".implode(' AND ', $where);
			if (count($groupby) > 0) $sql .= " GROUP BY " . implode(', ',$groupby);
			$sql .= " ORDER BY comment_date DESC LIMIT $limit"; 
			//echo $sql;
			$results = $wpdb->get_results($sql);
		} else {
			$results = false;
		}		
	    if ($results) {
			$translations = ppl_prepare_template($options['output_template']);
			if ($options['group_by'] === 'post') {
				$options['sort']['by1'] = '{date:raw}';
				$options['sort']['order1']  = SORT_DESC;
				$options['sort']['case1'] = 'false';
				$options['sort']['by2'] = '{commentdate:raw}';
				$options['sort']['order2']  = SORT_DESC;
				$options['sort']['case2'] = 'false';
			} elseif ($options['group_by'] === 'author') {
				$options['sort']['by1'] = '{commenter}';
				$options['sort']['order1']  = SORT_ASC;
				$options['sort']['case1'] = 'true';
				$options['sort']['by2'] = '{commentdate:raw}';
				$options['sort']['order2']  = SORT_DESC;
				$options['sort']['case2'] = 'false';
			}
			foreach ($results as $result) {
				$items[] = ppl_expand_template($result, $options['output_template'], $translations, 'recent-comments');
			}
			if ($options['sort']['by1'] !== '') $items = ppl_sort_items($options['sort'], $results, 'recent-comments', $options['group_template'], $items);		
			$output = implode(($options['divider']) ? $options['divider'] : "\n", $items);
			$output = $options['prefix'] . $output . $options['suffix'];
		} else {
			// if we reach here our query has produced no output ... so what next?
			if ($options['no_text'] !== 'false') {
				$output = ''; // we display nothing at all
			} else {
				// we display the blank message, with tags expanded if necessary
				$translations = ppl_prepare_template($options['none_text']);
				$output = $options['prefix'] . ppl_expand_template(array(), $options['none_text'], $translations, 'recent_comments') . $options['suffix'];
			}
		}
		if (defined('POC_CACHE_4')) poc_cache_store($cache_key, $output); 
		return ($output) ? $output . sprintf("<!-- Recent Comments took %.3f ms -->", 1000 * (ppl_microtime() - $start_time)) : '';
	}

	// tries to install the post-plugin-library plugin
	function install_post_plugin_library() {
		$plugin_path = 'post-plugin-library/post-plugin-library.php';
		$current = get_option('active_plugins');
		if (!in_array($plugin_path, $current)) {
			$current[] = $plugin_path;
			update_option('active_plugins', $current);
			do_action('activate_'.$plugin_path);
		}
	}

	function check_post_plugin_library($msg) {
		$exists = function_exists('ppl_microtime');
		if (!$exists) echo $msg;
		return $exists;
	}
	
}

if ( is_admin() ) {
	require(dirname(__FILE__).'/recent-comments-admin.php');
}

function widget_rrm_recent_comments_init() {
	if (! function_exists("register_sidebar_widget")) {
		return;
	}
	function widget_rrm_recent_comments($args) {
		extract($args);
		$options = get_option('widget_rrm_recent_comments');
		$opt = get_option('recent-comments');
		$widget_condition = $opt['widget_condition'];
		// the condition specified in the widget control overrides  the placement setting screen
		if ($options['condition']) {
			$condition = $options['condition'];
		} else {
			if ($widget_condition) {
				$condition = $widget_condition;
			} else {
				$condition = 'true';
			}
		}
		$condition = (stristr($condition, "return")) ? $condition : "return ".$condition;
		$condition = rtrim($condition, '; ') . ' || is_admin();'; 
		if (eval($condition)) {
			$title = empty($options['title']) ? __('Recent Comments', 'recent_comments_plugin') : $options['title'];
			if ( !$number = (int) $options['number'] )
				$number = 10;
			else if ( $number < 1 )
				$number = 1;
			else if ( $number > 15 )
				$number = 15;
			$options = get_option('recent-posts');	
			$widget_parameters = $options['widget_parameters'];
			$output = RecentComments::execute('limit='.$number.'&'.$widget_parameters);
			if ($output) {
				echo $before_widget;
				echo $before_title.$title.$after_title;
				echo $output;
				echo $after_widget;
			}
		}
	}
	function widget_rrm_recent_comments_control() {
		if ( $_POST['widget_rrm_recent_comments_submit'] ) {
			$options['title'] = strip_tags(stripslashes($_POST['widget_rrm_recent_comments_title']));
			$options['number'] = (int) $_POST["widget_rrm_recent_comments_number"];
			$options['condition'] = stripslashes(trim($_POST["widget_rrm_recent_comments_condition"], '; '));
			update_option("widget_rrm_recent_comments", $options);
		} else {
			$options = get_option('widget_rrm_recent_comments');
		}		
		$title = attribute_escape($options['title']);
		if ( !$number = (int) $options['number'] )
			$number = 5;
		$condition = attribute_escape($options['condition']);
		?>
		<p><label for="widget_rrm_recent_comments_title"> <?php _e('Title:', 'recent_comments_plugin'); ?> <input style="width: 200px;" id="widget_rrm_recent_comments_title" name="widget_rrm_recent_comments_title" type="text" value="<?php echo $title; ?>" /></label></p>
		<p><label for="widget_rrm_recent_comments_number"> <?php _e('Number of posts to show:', 'recent_comments_plugin'); ?> <input style="width: 25px; text-align: center;" id="widget_rrm_recent_comments_number" name="widget_rrm_recent_comments_number" type="text" value="<?php echo $number; ?>" /></label> <?php _e('(at most 15)', 'recent_comments_plugin'); ?> </p>
		<p><label for="widget_rrm_recent_comments_condition"> <?php _e('Show only if page: (e.g., <a href="http://codex.wordpress.org/Conditional_Tags" title="help">is_single()</a>)', 'recent_comments_plugin'); ?> <input style="width: 200px;" id="widget_rrm_recent_comments_condition" name="widget_rrm_recent_comments_condition" type="text" value="<?php echo $condition; ?>" /></label></p>
		<input type="hidden" id="widget_rrm_recent_comments_submit" name="widget_rrm_recent_comments_submit" value="1" />
		There are many more <a href="options-general.php?page=recent-comments.php">options</a> available.
		<?php
	}
	register_sidebar_widget(__('Recent Comments +', 'recent_comments_plugin'), 'widget_rrm_recent_comments');
	register_widget_control(__('Recent Comments +', 'recent_comments_plugin'), 'widget_rrm_recent_comments_control', 300, 100);
}

add_action('plugins_loaded', 'widget_rrm_recent_comments_init');

function recent_comments_init () {
	load_plugin_textdomain('recent_comments_plugin');
	$options = get_option('recent-comments');
	if ($options['content_filter'] === 'true' && function_exists('ppl_register_content_filter')) ppl_register_content_filter('RecentComments');
	if ($options['feed_on'] === 'true' && function_exists('ppl_register_post_filter')) ppl_register_post_filter('feed', 'recent-comments', 'RecentComments');
	if ($options['append_condition']) {
		$condition = $options['append_condition'];
	} else {
		$condition = 'true';
	}
	$condition = (stristr($condition, "return")) ? $condition : "return ".$condition;
	$condition = rtrim($condition, '; ') . ';'; 
	if ($options['append_on'] === 'true' && function_exists('ppl_register_post_filter')) ppl_register_post_filter('append', 'recent-comments', 'RecentComments', $condition);
}

add_action ('init', 'recent_comments_init', 1);

?>