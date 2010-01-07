<?php

//intro page
function stray_intro() {

	global $wpdb,$current_user;
	
	//load options
	$quotesoptions = array();
	$quotesoptions = get_option('stray_quotes_options');
	
	//security check
	if( $quotesoptions['stray_multiuser'] == false && !current_user_can('manage_options') )
		die('Access Denied');
	
	$widgetpage = get_option('siteurl')."/wp-admin/widgets.php";
	$management = get_option('siteurl')."/wp-admin/admin.php?page=stray_manage";
	$options =  get_option('siteurl')."/wp-admin/admin.php?page=stray_quotes_options";
	$new = get_option('siteurl')."/wp-admin/admin.php?page=stray_new";
	$help =  get_option('siteurl')."/wp-admin/admin.php?page=stray_help";
	$toolspage = get_option('siteurl')."/wp-admin/admin.php?page=stray_tools";
	$straymessage = $quotesoptions['stray_quotes_first_time'];
	
	//get total quotes
	$totalsql = "SELECT COUNT(`quoteID`) AS `Rows` FROM `" . WP_STRAY_QUOTES_TABLE . "` WHERE `user`='".$current_user->user_nicename."'";
	$totalquotes = $wpdb->get_var($totalsql);

	//feedback following activation (see main file)
	if ($straymessage !="") {
		
		?><div id="message" class="updated fade"><ul><?php echo $straymessage; ?></ul></div><?php
		
		//empty message after feedback
		$quotesoptions['stray_quotes_first_time'] = "";
		update_option('stray_quotes_options', $quotesoptions);
	}	
	
	?><div class="wrap"><h2>Stray Random Quotes: <?php _e('Overview','stray-quotes'); ?></h2><?php
	
	echo STRAY_DIR . '=stray_dir<br/>'. WP_STRAY_QUOTES_PATH . '=WP_STRAY_QUOTES_PATH<br/>';
	echo WP_CONTENT_URL . '=WP_CONTENT_URL<br/>'. WP_SITEURL . '=WP_SITEURL<br/>' 
. WP_PLUGIN_URL . '=WP_PLUGIN_URL<br/>' . WP_PLUGIN_DIR . '=WP_PLUGIN_DIR<br/><br/>';
echo ABSPATH . 'wp-content/plugins/' . STRAY_DIR . 'lang<br/>';
echo WP_PLUGIN_DIR. '/'. STRAY_DIR . 'lang<br/>';
	
	
	
    if ($totalquotes > 0) { 
	
		//quotes and categories
		$howmanycategories = count(make_categories($current_user->user_nicename));
		if ($howmanycategories == 1)$howmanycategories = __('one category','stray-quotes');
		else { 
			if ($howmanycategories)
				$howmanycategories = $howmanycategories . ' ' . __('categories','stray-quotes');
				$categorymost = mostused("category");	
		}		
		$sql = "SELECT COUNT( `category` ) AS `Rows` , `category` FROM `" . WP_STRAY_QUOTES_TABLE . "` WHERE `user`='".$current_user->user_nicename."' GROUP BY `category` ORDER BY `Rows` DESC";
		$howmany = $wpdb->get_results($sql);
		if ( count($howmany) > 1) $as = __(', distributed as follows:','stray-quotes');
		else $as = '.';
        $search = array('%s1','%s2', '%s3');
        $replace = array($totalquotes, $howmanycategories, $as);
        echo str_replace ($search,$replace, __('<p>Right now you have <strong>%s1 quotes</strong> in <strong>%s2</strong>%s3</p>','stray-quotes'));
		if ($howmany && count($howmany) > 1) { ?>
		
			<table class="widefat" style="width:200px"><?php
				
			$i = 0;
			
			foreach ( $howmany as $many ) {
			
				$alt = ($i % 2 == 0) ? ' class="alternate"' : '';
				
				?><tr <?php echo($alt); ?>>
				<th scope="row"><?php echo $many->Rows; ?></th>
				<td><?php echo $many->category; ?></td>
				</tr><?php 
			} ?>
			</table><?php	
		}		
		
		//visible quotes
		$visiblequotes = $wpdb->get_var("SELECT COUNT(`quoteID`) as rows FROM " . WP_STRAY_QUOTES_TABLE . " WHERE visible='yes' AND `user`='".$current_user->user_nicename."'"); 
		if($visiblequotes == $totalquotes)$visiblequotes = __('All your quotes ','stray-quotes');
		echo str_replace ('%s3',$visiblequotes, __('<p><strong>%s3</strong> are visible.</p>','stray-quotes'));
		
		//author
		$authormost = mostused("author");
		if ($authormost) echo str_replace ('%s5',$authormost, __('<p>Your most quoted author is <strong>%s5</strong>.</p>','stray-quotes'));
		
		//source
		$sourcemost = mostused("source");
		if ($sourcemost) str_replace ('%s5',$sourcemost, __('<p>Your most used source is <strong>%s5</strong>.</p>','stray-quotes'));
		
    } else _e('There is nothing to report.','stray-quotes');
    ?><p><?php
	
	//link pages
    $search = array ("%s1", "%s2");
	$replace = array($new,$management);	
	echo str_replace($search,$replace,__('To start doing stuff, you can <a href="%s1"><strong>add new quotes</strong></a>;<br />use the <a href="%s2"><strong>manage</strong></a> page to edit or delete existing quotes;','stray-quotes')); 
    
    if(current_user_can('manage_options')) echo str_replace("%s3",$options,__('<br />change the <a href="%s3"><strong>settings</strong></a> to control how the quotes are displayed on your blog;','stray-quotes'));
	
	$search2 = array ("%s4","%s5");
    $replace2 = array($help,$toolspage);	
	echo str_replace($search2,$replace2,__('<br/>a <a href="%s5"><strong>tools page</strong></a> can help you do more;<br/>if you\'re new to all this, visit the <a href="%s4"><strong>help page</strong></a>.','stray-quotes')); ?>
    
	</p>
    
    <p><?php _e('Brought to you by <a href="http://code.italyisfalling.com">lines of code</a>','stray-quotes'); ?>, <?php echo date('Y'); ?>.<br/><?php _e('Happy quoting.','stray-quotes'); ?></p><br/>
    
    <?php //donate ?>
	<p><form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
">
<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1"style="padding:0;border:none">
</form></p></div><?php
	
}
?>