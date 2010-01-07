<?php
/*
Plugin Name: Category Posts Widget
Plugin URI: http://jameslao.com/2009/07/29/category-posts-widget-2-0/
Description: Adds a widget that can display a specified number of posts from a single category. Can also set how many widgets to show.
Author: James Lao	
Version: 2.3
Author URI: http://jameslao.com/
*/

class CategoryPosts extends WP_Widget {

function CategoryPosts() {
	parent::WP_Widget(false, $name='Category Posts');
}

/**
 * Displays category posts widget on blog.
 */
function widget($args, $instance) {
	global $post, $wp_query;
	extract( $args );
	
	// If not title, use the name of the category.
	if( !$instance["title"] ) {
		$category_info = get_category($instance["cat"]);
		$instance["title"] = $category_info->name;
	}
	
	// Get array of post info.
	$cat_posts = new WP_Query("showposts=" . $instance["num"] . "&cat=" . $instance["cat"]);
	
	echo $before_widget;
	
	// Widget title

	echo $before_title;
	if( (bool) $instance["title_link"] )
		echo '<a href="' . get_category_link($instance["cat"]) . '">' . $instance["title"] . '</a>';
	else
		echo $instance["title"];
	echo $after_title;

	// Post list
	echo "<ul>\n";
	
	while ( $cat_posts->have_posts() ) : $cat_posts->the_post();
	?>
		<li class='cat-post-item'>

		<?php if ( (bool) $instance["thumbnail"] && function_exists('p75HasThumbnail') && p75HasThumbnail($cat_posts->post->ID) ) { ?>
		
			<a href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><img class='alignleft' src='<?php echo p75GetThumbnail($post->ID, $instance["thumbnail_width"], $instance["thumbnail_height"]); ?>' alt='<?php the_title_attribute(); ?>' /></a>
		
		<?php } // end thumbnail function check ?>

			<a class="post-title" href="<?php the_permalink(); ?>" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a>

			<?php if ( $instance['excerpt'] ) the_excerpt(); ?>
		</li>
<?php
	endwhile;
	
	echo "</ul>\n";
	
	echo $after_widget;
}

/**
 * Form processing... Dead simple.
 */
function update($new_instance, $old_instance) {
    $new_instance["cat"] = absint( $new_instance["cat"] );
    $new_instance["exclude"] = (bool) $new_instance["exclude"];
    
	return $new_instance;
}

/**
 * The configuration form.
 */
function form($instance) {
?>
		<p>
			<label for="<?php echo $this->get_field_id("title"); ?>">
				<?php _e( 'Title' ); ?>:
				<input class="widefat" id="<?php echo $this->get_field_id("title"); ?>" name="<?php echo $this->get_field_name("title"); ?>" type="text" value="<?php echo esc_attr($instance["title"]); ?>" />
			</label>
		</p>
		
		<p>
			<label>
				<?php _e( 'Category' ); ?>:
				<?php wp_dropdown_categories( array( 'name' => $this->get_field_name("cat"), 'selected' => $instance["cat"] ) ); ?>
			</label>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id("num"); ?>">
				<?php _e('Number of posts to show'); ?>:
				<input style="text-align: center;" id="<?php echo $this->get_field_id("num"); ?>" name="<?php echo $this->get_field_name("num"); ?>" type="text" value="<?php echo absint($instance["num"]); ?>" size='3' />
			</label>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id("title_link"); ?>">
				<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("title_link"); ?>" name="<?php echo $this->get_field_name("title_link"); ?>"<?php checked( (bool) $instance["title_link"], true ); ?> />
				<?php _e( 'Make widget title link' ); ?>
			</label>
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id("excerpt"); ?>">
				<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("excerpt"); ?>" name="<?php echo $this->get_field_name("excerpt"); ?>"<?php checked( (bool) $instance["excerpt"], true ); ?> />
				<?php _e( 'Show post excerpt' ); ?>
			</label>
		</p>
		
		<?php if ( function_exists("p75GetThumbnail") ) : ?>
		<p>
			<label for="<?php echo $this->get_field_id("thumbnail"); ?>">
				<input type="checkbox" class="checkbox" id="<?php echo $this->get_field_id("thumbnail"); ?>" name="<?php echo $this->get_field_name("thumbnail"); ?>"<?php checked( (bool) $instance["thumbnail"], true ); ?> />
				<?php _e( 'Show post thumbnail' ); ?>
			</label>
		</p>
		<p>
			<label>
				<?php _e('Thumbnail dimensions'); ?>:<br />
				<label for="<?php echo $this->get_field_id("thumbnail_width"); ?>">
					W: <input class="widefat" style="width:40%;" type="text" id="<?php echo $this->get_field_id("thumbnail_width"); ?>" name="<?php echo $this->get_field_name("thumbnail_width"); ?>" value="<?php echo $instance["thumbnail_width"]; ?>" />
				</label>
				
				<label for="<?php echo $this->get_field_id("thumbnail_height"); ?>">
					H: <input class="widefat" style="width:40%;" type="text" id="<?php echo $this->get_field_id("thumbnail_height"); ?>" name="<?php echo $this->get_field_name("thumbnail_height"); ?>" value="<?php echo $instance["thumbnail_height"]; ?>" />
				</label>
			</label>
		</p>
		<?php endif; ?>

<?php

}

}

add_action( 'widgets_init', create_function('', 'return register_widget("CategoryPosts");') );

?>
