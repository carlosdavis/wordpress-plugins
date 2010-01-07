<?php function stray_tools(){
	
	global $current_user;
	
	//get the options
	$quotesoptions = array();
	$quotesoptions = get_option('stray_quotes_options');
	
	//security check
	if( $quotesoptions['stray_multiuser'] == false && !current_user_can('manage_options') )
		die('Access Denied');
	
	if(!empty($_POST['do'])) {
		
		//function to change bookmarklet options
		if(isset($_POST['boptions'])){
		
			$quotesoptions = array();
			$quotesoptions = get_option('stray_quotes_options');			
			$quotesoptions['bookmarklet_cat'] = $_POST['categories'];	
			$quotesoptions['bookmarlet_source'] = $_POST['websource'];	

			$update_shortcodes = update_option('stray_quotes_options', $quotesoptions);			
			
			//feedback
			if ($update_shortcodes) { ?>
                <div id="message" class="updated fade below-h2"><p>
                <?php _e('<strong>Bookmarklet options saved.</strong>','stray-quotes'); ?></p></div><?php
			} else {
				?><div id="message" class="error fade below-h2"><p>
				<?php _e('<strong>Bookmarklet options not saved. Something went wrong!</strong>','stray-quotes'); ?></p></div><?php
            }
		}
		
		//function to enable shortcodes
		else if( isset($_POST['enable']) &&  current_user_can('manage_options') ){
			
			$quotesoptions = array();
			$quotesoptions = get_option('stray_quotes_options');			
				
			$quotesoptions['comment_scode'] = $_POST['comment_scode'];	
			$quotesoptions['title_scode'] = $_POST['title_scode'];	
			$quotesoptions['excerpt_scode'] = $_POST['excerpt_scode'];		
			$quotesoptions['widget_scode'] = $_POST['widget_scode'];
			$quotesoptions['categories_scode'] = $_POST['categories_scode'];		
			$quotesoptions['tags_scode'] = $_POST['tags_scode'];		
			$quotesoptions['bloginfo_scode'] = $_POST['bloginfo_scode'];

			$update_shortcodes = update_option('stray_quotes_options', $quotesoptions);			
			
			//feedback
			if ($update_shortcodes) { ?>
                <div id="message" class="updated fade below-h2"><p>
                <?php _e('<strong>Shortcodes enabled.</strong>','stray-quotes'); ?></p></div><?php
			} else {
				?><div id="message" class="error fade below-h2"><p>
				<?php _e('<strong>Shortcodes not enabled. Something went wrong!</strong>','stray-quotes'); ?></p></div><?php
            }

		} 
		
		//function reset id numbers
		else if( isset($_POST['submit']) &&  current_user_can('manage_options') ){
		
			global $wpdb;
			$query1 = $wpdb->query("ALTER TABLE `".WP_STRAY_QUOTES_TABLE."` DROP `quoteID`");
			$query2 = $wpdb->query("ALTER TABLE `".WP_STRAY_QUOTES_TABLE."` ADD COLUMN `quoteID` INT NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST");
				
			if ($query1 && $query2) { ?>
                <div id="message" class="updated fade below-h2"><p>
                <?php echo str_replace("%s",get_option("siteurl").'/wp-admin/admin.php?page=stray_manage' ,__('<strong>Quote IDs have been reset.</strong> To review use the <a href="%s">Manage page</a>.','stray-quotes'));  ?></p></div><?php 
			} else { ?>
                <div id="message" class="error fade below-h2"><p>
                <?php _e('<strong>Failure.</strong> It was not possible to reset the ID numbers.','stray-quotes');
                ?></p></div><?php 
			}			
		} 
		
		//function to reset the options
		else if( isset($_POST['resetsettings']) &&  current_user_can('manage_options') ){
		
			$quotesoptions = array(
								   
					'stray_quotes_before_all' =>  '',
					'stray_quotes_before_quote' =>  '&#8220;',   
					'stray_quotes_after_quote' =>  '&#8221;',   
					'stray_quotes_before_author' =>  '<br/>by&nbsp;',		   
					'stray_quotes_after_author'	 =>  '',
					'stray_quotes_before_source' =>  '<em>&nbsp;',
					'stray_quotes_after_source'	 =>  '</em>',		   
					'stray_quotes_after_all' =>  '',
					'stray_quotes_put_quotes_first' =>  'Y',
					'stray_quotes_default_visible' =>  'Y',
					'stray_quotes_linkto' =>  '',
					'stray_quotes_sourcelinkto' =>  '',
					'stray_quotes_sourcespaces' => '-',
					'stray_quotes_authorspaces'=> '-',
					'stray_if_no_author' =>  '<br/>source:&nbsp;',
					'stray_quotes_uninstall' => '',
					'stray_clear_form' =>  'Y',
					'stray_quotes_order' => 'quoteID',
					'stray_quotes_rows' => 10,
					'stray_quotes_categories' => 'all',
					'stray_quotes_sort' => 'DESC',
					'stray_default_category' =>  'default',
					'stray_quotes_version' => WP_STRAY_VERSION,
					'stray_before_loader' => '<p align=>"left">',
					'stray_loader' => '',
					'stray_after_loader' => '</p>',
					'stray_ajax' =>  '',
					'comment_scode' =>  '',
					'title_scode' =>  '',
					'excerpt_scode' =>  '',
					'widget_scode' =>  '',
					'categories_scode' =>  '',
					'tags_scode' =>  '',
					'bloginfo_scode' =>  '',
					'bookmarlet_source' =>  '',
					'bookmarklet_cat' =>  '',
					'stray_loading' =>  __('loading...', 'stray-quotes'),
					'stray_quotes_first_time' => ''
					
			);
			
			$updateoptions = update_option('stray_quotes_options', $quotesoptions);		

			//feedback
			if ($updateoptions) { ?>
                <div id="message" class="updated fade below-h2"><p>
                <?php _e('<strong>The settings have been reset to factory defaults.</strong>','stray-quotes'); ?></p></div><?php
			} else {
				?><div id="message" class="error fade below-h2"><p>
				<?php _e('<strong>Settings not reset. Something went wrong!</strong>','stray-quotes'); ?></p></div><?php
            }
		}
	}
	
	if ( $quotesoptions['comment_scode'] == 'Y' ) $comment_scode_selected =  'checked';	
	if ( $quotesoptions['title_scode'] == 'Y' ) $title_scode_selected = 'checked';	
	if ( $quotesoptions['excerpt_scode'] == 'Y' ) $excerpt_scode_selected  = 'checked';	
	if ( $quotesoptions['widget_scode'] == 'Y' ) $widget_scode_selected = 'checked';
	if ( $quotesoptions['tags_scode'] == 'Y' ) $tags_scode_selected = 'checked';	
	if ( $quotesoptions['categories_scode'] == 'Y' ) $categories_scode_selected = 'checked';	
	if ( $quotesoptions['bloginfo_scode'] == 'Y' ) $bloginfo_scode_selected = 'checked';
	if ( $quotesoptions['bookmarlet_source'] == 'Y' ) $websource_selected = 'checked';
	$category = $quotesoptions['bookmarklet_cat'];

	?><div class="wrap">
	<h2><?php _e('Tools for Stray Random Quotes', 'stray-quotes'); ?></h2>
    
    <?php //the bookmarklet ?>
	<p><h3>Bookmarklet</h3></p>
	<blockquote>
	<p><?php _e('To create quotes on the fly, drag the link below to your bowser toolbar.', 'stray-quotes'); ?>
	<br/><span class="setting-description"><?php _e('How to use: When you find some text in a web page that you want to turn into a quote, select the text and click on the link.', 'stray-quotes'); ?></span></p><p><strong><a href="<?php echo "javascript:if(navigator.userAgent.indexOf('Safari')%20>=%200){Q=getSelection();}else{Q=document.selection?document.selection.createRange().text:document.getSelection();}void(window.open('". get_option('siteurl'). "/wp-admin/admin.php?page=stray_new&action=bookmarklet&quote_quote='+encodeURIComponent(Q)+'&quote_source=<a%20href='+encodeURIComponent(location.href)+'>'+encodeURIComponent(document.title)+'</a>'));"; ?>"><?php _e('Quote this', 'stray-quotes'); ?></a></strong></p>
    
    <?php if(current_user_can('manage_options')) { ?><form name="frm_bookmarklet" action="<?php echo ($_SERVER['REQUEST_URI']); ?>" method="post">
    <p><?php _e('Default category for bookmarklet quotes: ', 'stray-quotes'); ?><select name="categories" style="vertical-align:middle; width:14em;"> 
	<?php $categorylist = make_categories(); 
    foreach($categorylist as $categoryo){ ?>
    <option value="<?php echo $categoryo; ?>" style=" padding-right:5px"
    <?php  if ( $categoryo == $category) echo ' selected'; ?> >
    <?php echo $categoryo;?></option>
    <?php } ?>   
    </select><br/>
    <input type="checkbox" name="websource" value="Y" <?php echo ($websource_selected); ?> />&nbsp;<?php _e('If checked, will add a link to the web page as source for the quote.', 'stray-quotes'); ?><br/><span class="setting-description"><?php _e('Note: no matter how you change these options, the bookmarklet will stay the same', 'stray-quotes'); ?>.</span></p>	
    <p class="submit"><input type="hidden" name="do" value="Update" />
    <input type="submit" name="boptions" value="<?php _e('Apply bookmarklet options', 'stray-quotes'); ?>">	
	</p></form><?php } ?>
	
	<?php if(!current_user_can('manage_options'))die(''); ?>
	</blockquote>
    	
	 <?php //the shortcodes ?>
	<p><h3><?php _e('Add shortcodes everywhere', 'stray-quotes'); ?></h3></p>
	<blockquote><form name="frm_scode" action="<?php echo ($_SERVER['REQUEST_URI']); ?>" method="post">
    <p><?php _e('For some reason, you might want to add random things <em>via shortcodes</em> to other parts of your blog other than posts and pages. Wordpress does not support this, but a patch is pretty easy to apply. All you have to do is select here below the areas where you want to be able to use shortcodes. This method will allow you to have things such as random taglines or random category names the easiest way.', 'stray-quotes'); ?>
    <br/>	<span class="setting-description">
    <?php _e('Note that this will apply to all sorts of shortcodes and not only to those of Stray Random Quotes. Consider this carefully when you enable it for an area accessible to everyone, such as comments. If you don\'t understand any of this, don\'t use it.', 'stray-quotes'); ?></span></p>
    <ul><li><input type="checkbox" name="comment_scode" value="Y" <?php echo ($comment_scode_selected); ?> />
	<?php _e('Enable shortcodes in <strong>comments</strong>', 'stray-quotes'); ?></li>
    <li><input type="checkbox" name="title_scode" value="Y" <?php echo ($title_scode_selected); ?> />
	<?php _e('Enable shortcodes in <strong>post titles</strong>', 'stray-quotes'); ?></li>
    <li><input type="checkbox" name="excerpt_scode" value="Y" <?php echo ($excerpt_scode_selected); ?> />
	<?php _e('Enable shortcodes in <strong>post excerpts</strong>', 'stray-quotes'); ?></li>
    <li><input type="checkbox" name="categories_scode" value="Y" <?php echo ($categories_scode_selected); ?> />
	<?php _e('Enable shortcodes in <strong>categories</strong>', 'stray-quotes'); ?></li>
    <li><input type="checkbox" name="tags_scode" value="Y" <?php echo ($tags_scode_selected); ?> />
	<?php _e('Enable shortcodes in <strong>tags</strong>', 'stray-quotes'); ?></li>
    <li><input type="checkbox" name="bloginfo_scode" value="Y" <?php echo ($bloginfo_scode_selected); ?> />
	<?php _e('Enable shortcodes in <strong>bloginfo (such as blog title or the tagline)</strong>', 'stray-quotes'); ?></li>
    <li><input type="checkbox" name="widget_scode" value="Y" <?php echo ($widget_scode_selected); ?> />
	<?php _e('Enable shortcodes in <strong>text widgets</strong>', 'stray-quotes'); ?></li></ul>
    <p class="submit">&nbsp;<input type="hidden" name="do" value="Update" /><input type="submit" name="enable" value="<?php _e('Toggle shortcodes', 'stray-quotes'); ?>">	
    </p>
	</form></blockquote>
    
	<?php //the index reset ?>
    <p><h3><?php _e('Reset the index', 'stray-quotes'); ?></h3></p>
    <blockquote><form name="frm_index" action="<?php echo ($_SERVER['REQUEST_URI']); ?>" method="post">
    <p><?php _e('If you want to reset the numbering of your quotes, click on the button below. If you are the cautious type, maybe you want to backup first.', 'stray-quotes'); ?>
    <br/><span class="setting-description"><?php _e('Note that after this some (or all) IDs might not correspond to the same quotes.', 'stray-quotes'); ?></span></p>
    <p class="submit">&nbsp;<input type="hidden" name="do" value="Update" /><input type="submit" name="submit" value="<?php _e('Reset index', 'stray-quotes'); ?>">	
    </p>
    </form></blockquote>

	 <?php //reset options ?>
	<p><h3><?php _e('Reset the Settings', 'stray-quotes'); ?></h3></p>
	<blockquote><form name="frm_reset" action="<?php echo ($_SERVER['REQUEST_URI']); ?>" method="post">
    <p><?php _e('If you want the defaults settings back, click on the button below.', 'stray-quotes'); ?>
    <br/>	<span class="setting-description">
    <?php _e('This will revert all the settings to factory defaults according to recent issues of the plugin. They might differ from what they were many versions ago.', 'stray-quotes'); ?></span></p>
    
    <p class="submit">&nbsp;<input type="hidden" name="do" value="Update" /><input type="submit" name="resetsettings" value="<?php _e('Reset Settings', 'stray-quotes'); ?>">	
    </p>
	</form></blockquote>
        
	</div><?php

} ?>