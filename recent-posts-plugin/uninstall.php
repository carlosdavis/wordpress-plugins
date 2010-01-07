<?php
	if (defined('ABSPATH') && defined('WP_UNINSTALL_PLUGIN') 
	&& strtolower(WP_UNINSTALL_PLUGIN) === 'recent-posts-plugin/recent-posts.php') {
		delete_option('recent-posts');
		delete_option('widget_rrm_recent_posts');
	}
?>