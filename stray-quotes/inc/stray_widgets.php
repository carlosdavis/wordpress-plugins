<?php
/*I'll add comments to this section soon.*/
/* NOTE: whenever on this page you find "group" mentioned, it is meant "category". 
But it has to remain "group", to avoid having to change the 'widget_stray_quotes' array structure in the database. */

class stray_widgets {

    function init() {
		
        if (!$options = get_option('widget_stray_quotes'))
            $options = array();
            
        $widget_ops = array('classname' => 'widget_stray_quotes', 'description' => '');
        $control_ops = array('width' => 650, 'height' => 100, 'id_base' => 'stray_widgets');
        $name = 'Quotes';
        
        $registered = false;
        foreach (array_keys($options) as $o) {
            if (!isset($options[$o]['title']))
                continue;
                
            $id = "stray_widgets-$o";		
				
			//check if the widgets is active
			global $wpdb;		
			$sql = "SELECT option_value FROM $wpdb->options WHERE option_name = 'sidebars_widgets' AND option_value like '%".$id."%'";
			$var = $wpdb->get_var( $sql );
			//do this to keep the size of the array down
			if (!$var)unset($options[$o]);
			
            $registered = true;
            wp_register_sidebar_widget($id, $name, array(&$this, 'widget'), $widget_ops, array( 'number' => $o ) );
            wp_register_widget_control($id, $name, array(&$this, 'control'), $control_ops, array( 'number' => $o ) );
        }
        if (!$registered) {
            wp_register_sidebar_widget('stray_widgets-1', $name, array(&$this, 'widget'), $widget_ops, array( 'number' => -1 ) );
            wp_register_widget_control('stray_widgets-1', $name, array(&$this, 'control'), $control_ops, array( 'number' => -1 ) );
        }
		
		update_option('widget_stray_quotes', $options);
    }

    function widget($args, $widget_args = 1) {
        extract($args);

        if (is_numeric($widget_args))
            $widget_args = array('number' => $widget_args);
        $widget_args = wp_parse_args($widget_args, array( 'number' => -1 ));
        extract($widget_args, EXTR_SKIP);
        $options_all = get_option('widget_stray_quotes');
        if (!isset($options_all[$number]))
            return;

        $options = $options_all[$number];
		
		if ($options["sequence"] == "Y")$sequence = false;
		else $sequence = true;
		
		if ($options["noajax"] == "Y")$noajax = true;
		else $noajax = false;
		
		if ($options["disableaspect"] == "Y")$disableaspect = true;
		else $disableaspect = false;
	
		$linkphrase = $options["linkphrase"];
		$widgetid = $number;
		$multi = $options["multi"];settype($multi, "integer");
		$categories = isset($options["groups"]) ? explode(',', $options["groups"]) : array("default");
		$offset = 0;
		$timer =  $options["timer"];
		$fullpage = false;
		
		$quotesoptions = get_option('stray_quotes_options');
		if ($quotesoptions['stray_multiuser']=='Y')
			$contributor = $options["contributor"];
		else $contributor = '';
		
		//output the quote(s)
        echo $before_widget.$before_title;
		echo $options["title"];
        echo $after_title;
		echo get_stray_quotes($categories,$sequence,$linkphrase,$multi,$timer,$noajax,$offset,$widgetid,$fullpage,'quoteID','ASC','',$disableaspect,$contributor);
        echo $after_widget;
    }

    function control($widget_args = 1) {
        global $wp_registered_widgets;
        static $updated = false;
		$quotesoptions = get_option('stray_quotes_options');
		
		//extract widget arguments
        if ( is_numeric($widget_args) )$widget_args = array('number' => $widget_args);
        $widget_args = wp_parse_args($widget_args, array('number' => -1));
        extract($widget_args, EXTR_SKIP);
		
        $options_all = get_option('widget_stray_quotes');
        if (!is_array($options_all))$options_all = array();  
            
        if (!$updated && !empty($_POST['sidebar'])) {
            $sidebar = (string)$_POST['sidebar'];

            $sidebars_widgets = wp_get_sidebars_widgets();
            if (isset($sidebars_widgets[$sidebar]))
                $this_sidebar =& $sidebars_widgets[$sidebar];
            else
                $this_sidebar = array();

            foreach ($this_sidebar as $_widget_id) {
                if ('widget_stray_quotes' == $wp_registered_widgets[$_widget_id]['callback'] && isset($wp_registered_widgets[$_widget_id]['params'][0]['number'])) {
                    $widget_number = $wp_registered_widgets[$_widget_id]['params'][0]['number'];
                    if (!in_array("stray_widgets-$widget_number", $_POST['widget-id']))
                        unset($options_all[$widget_number]);
                }
            }
            foreach ((array)$_POST['widget_stray_quotes'] as $widget_number => $posted) {
                if (!isset($posted['title']) && isset($options_all[$widget_number]))
                    continue;
                
                $options = array();
                
                $options['title'] = $posted['title'];
                $options['groups'] = isset($posted['groups']) ? implode(',', $posted['groups']) : ''; 
				$options['sequence'] =  $posted['sequence'];
				$options['linkphrase'] =  $posted['linkphrase'];
				$options['timer'] =  $posted['timer'];
				$options['noajax'] =  $posted['noajax'];
				$options['multi'] =  $posted['multi'];
				$options['disableaspect'] =  $posted['disableaspect'];
				$options['contributor'] =  $posted['contributor'];
                
                $options_all[$widget_number] = $options;
            }
            update_option('widget_stray_quotes', $options_all);
            $updated = true;
        }
		
		$default_options = array(
				'title' => __('Random Quote', 'stray-quotes'), 
				'groups' => implode(",",make_categories()),
				'sequence' => true,
				'multi' => '1',
				'linkphrase' => $quotesoptions['stray_loader'],
				'timer' => '0',
				'noajax' => false,
				'disableaspect' => false,
				'contributor' => ''
		);
	

        if (-1 == $number) {
            $number = '%i%';
            $values = $default_options;
        }
        else {
            $values = $options_all[$number];
        }
		
		if ( $values['sequence'] == "Y" ) $random_selected = ' checked="checked"';	
		if ( $values['noajax'] == "Y" ) $noajax_selected = ' checked="checked"';
		if ( $values['disableaspect'] == "Y" ) $disableaspect_selected = ' checked="checked"';
		if ( $values['timer'] == '' )$values['timer'] = '0';
		if ( $values['multi'] == '' )$values['multi'] = '1';
        
		// widget options form ?>
        <p align="right"><span class="setting-description"><small><?php _e('all settings are for this widget only.', 'stray-quotes')?></small></span></p>
        <p><label><strong><?php _e('Title', 'stray-quotes')?></strong></label>
		<input class="widefat" id="widget_stray_quotes-<?php echo $number; ?>-title" 
        name="widget_stray_quotes[<?php echo $number; ?>][title]" type="text" 
        value="<?php echo htmlspecialchars($values['title'], ENT_QUOTES); ?>" />
        </p>
        
        <div style="float:left; width:210px; padding-right:10px;">
        
        <?php 
			if ($quotesoptions['stray_multiuser']=='Y') $height = '70px';
			else $height = '130px';		
        ?>
        
        
		<p><label><strong><?php _e('Categories', 'stray-quotes')?></strong><br/><span class="setting-description"><small><?php _e('Quotes are taken from these categories. Drag the mouse or ctrl-click to multi-select', 'stray-quotes')?></small></span></label>
		<select class="widefat" style="width: 100%; height: <?php echo $height; ?>;" name="widget_stray_quotes[<?php echo $number; ?>][groups][]" 
        id="widget_stray_quotes-<?php echo $number; ?>-groups" multiple="multiple">
		<?php 
		$items = make_categories();
		$groups = explode(',', $values['groups']);
        if ($items) {
            foreach ($items as $item) {
                if (in_array($item, $groups))
                    $current = ' selected="selected"';
                else
                    $current = '';
    
                echo "\n\t<option value='$item'$current>$item</option>";        
            }
        }         
		?></select></p>
        
        <?php if ($quotesoptions['stray_multiuser']=='Y') { ?>
        <p><label><strong><?php _e('Only from this contributor', 'stray-quotes')?></strong><br/><span class="setting-description"> <small> <?php _e('if left empty, quotes are taken from all contributors.', 'stray-quotes')?></small></span></label>
		<input class="widefat" id="widget_stray_quotes-<?php echo $number; ?>-user" 
        name="widget_stray_quotes[<?php echo $number; ?>][contributor]" type="text" 
        value="<?php echo htmlspecialchars($values['contributor'], ENT_QUOTES); ?>" /></p>
        <?php } ?>
 
 		</div>
        <div style="float:left; width:200px; border-left:1px #ccc solid; padding-left:10px;">
        
		<p><input type="checkbox" name="widget_stray_quotes[<?php echo $number; ?>][sequence]" value="Y" <?php echo $random_selected; ?> /><label><strong><?php _e('Random', 'stray-quotes')?></strong><br/><span class="setting-description"><small><?php _e('Leave unckecked to load the quotes in order beginning from a random one.', 'stray-quotes')?></small></span></label><br />&nbsp;</p>
        
 		
		<p><strong><?php _e('Show', 'stray-quotes')?></strong>&nbsp;<input type="text" style="border: 1px solid #ccc;width:40px" name="widget_stray_quotes[<?php echo $number; ?>][multi]" value="<?php echo $values['multi']; ?>" maxlength="4" />&nbsp;<strong><?php _e('quotes at a time', 'stray-quotes')?></strong><br />&nbsp;</p>
        
        <p><input type="checkbox" name="widget_stray_quotes[<?php echo $number; ?>][disableaspect]" value="Y" <?php echo $disableaspect_selected; ?> /><label><strong><?php _e('Disable aspect settings', 'stray-quotes')?></strong><br/><span class="setting-description"><small><?php echo str_replace("%s1",get_option('siteurl')."/wp-admin/admin.php?page=stray_quotes_options",__('As set in the "How the quotes look" section of the <a href="%s1">settings page</a>.', 'stray-quotes'))?></small></span></label></p>
        
        
        </div>
        
        <div style="float:right; width:200px; height:100%; border-left:1px #ccc solid; padding-left:10px;"><!--<p><?php _e('AJAX functionality:', 'stray-quotes')?></p>-->
		<p><label><strong><?php _e('Reload phrase', 'stray-quotes')?></strong><br/><span class="setting-description"> <small> <?php _e('if left empty, reloading is done by clicking on the quote area.', 'stray-quotes')?></small></span></label>
		<input class="widefat" id="widget_stray_quotes-<?php echo $number; ?>-title" 
        name="widget_stray_quotes[<?php echo $number; ?>][linkphrase]" type="text" 
        value="<?php echo htmlspecialchars($values['linkphrase'], ENT_QUOTES); ?>" /></p>
        

        
        <p><strong><?php _e('Rotate every', 'stray-quotes')?></strong>&nbsp;<input type="text" style="border: 1px solid #ccc;width:30px" name="widget_stray_quotes[<?php echo $number; ?>][timer]" value="<?php echo $values['timer']; ?>" />&nbsp;<strong><?php _e('seconds', 'stray-quotes')?></strong><br/><span class="setting-description"><small> <?php _e('Reload the quote after the set interval. This hides the link phrase. Leave this empty or set it to 0 to disable.', 'stray-quotes')?></small></span></p>
        


		<p><input type="checkbox" name="widget_stray_quotes[<?php echo $number; ?>][noajax]" value="Y" <?php echo $noajax_selected; ?> /><label><strong><?php _e('Disable AJAX', 'stray-quotes')?></strong><br/><span class="setting-description"><small> <?php _e('Check to disable any dynamic reloading of the quote.', 'stray-quotes')?></small></span></label></p>
        
        </div>     
        <div style="clear:both;">&nbsp;</div><?php	
        
	}
}

$gdm = new stray_widgets();
add_action('widgets_init', array($gdm, 'init'));

?>