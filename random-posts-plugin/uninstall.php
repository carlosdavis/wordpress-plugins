<?php
	if (defined('ABSPATH') && defined('WP_UNINSTALL_PLUGIN') 
	&& strtolower(WP_UNINSTALL_PLUGIN) === 'random-posts-plugin/random-posts.php') {
		delete_option('random-posts');
		delete_option('widget_rrm_random_posts');
	}
?>