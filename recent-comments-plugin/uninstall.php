<?php
	if (defined('ABSPATH') && defined('WP_UNINSTALL_PLUGIN') 
	&& strtolower(WP_UNINSTALL_PLUGIN) === 'recent-comments-plugin/recent-comments.php') {
		delete_option('recent-comments');
		delete_option('widget_rrm_recent_comments');
	}
?>