<?php 

//manage page
function stray_manage() {
    	
	global $wpdb,$current_user;
	
	//load options
	$quotesoptions = get_option('stray_quotes_options');
	
	//security check
	if( $quotesoptions['stray_multiuser'] == false && !current_user_can('manage_options') )
		die('Access Denied');
	
	//decode and intercept
	foreach($_POST as $key => $val)$_POST[$key] = stripslashes($val);
	 		
	//defaults and gets
	$action = !empty($_REQUEST['qa']) ? $_REQUEST['qa'] : '';
	$quoteID = !empty($_REQUEST['qi']) ? $_REQUEST['qi'] : '';
	
	$orderby = $quotesoptions['stray_quotes_order'];
	$pages = 1;
	$rows = $quotesoptions['stray_quotes_rows']; 
	$categories = $quotesoptions['stray_quotes_categories']; 
	$sort = $quotesoptions['stray_quotes_sort']; 
	
	if(isset($_GET['qo'])){
		$orderby = $_GET['qo'];
		$quotesoptions['stray_quotes_order'] = $_GET['qo'];
	}
	if(isset($_GET['qp']))$pages = $_GET['qp'];	
	
	if(isset($_GET['qr'])){
		$rows = $_GET['qr'];
		$quotesoptions['stray_quotes_rows'] = $_GET['qr'];	
	}
	
	if(isset($_GET['qc'])){
		$categories = $_GET['qc'];
		$quotesoptions['stray_quotes_categories'] = $_GET['qc'];	
	}
	
	if(isset($_GET['qs'])){
		$sort = $_GET['qs'];
		$quotesoptions['stray_quotes_sort'] = $_GET['qs'];		
	}
	
	$offset = ($pages - 1) * $rows;
	
	//check if the category I want exists
	$ok = false;
	$categorylist = make_categories(); 
	foreach($categorylist as $category){ 
		if ($category == $categories) $ok = true;
	}		
	if ($ok == false) {
		$categories = 'all';
		$quotesoptions['stray_quotes_categories'] = 'all';
	}
	
	//update options now
	update_option('stray_quotes_options', $quotesoptions);
	
	//add variables to the url -- for different uses -- thanks to frettsy who suggested this use
	$baseurl = get_option("siteurl").'/wp-admin/admin.php?page=stray_manage';
	$baseurl = querystrings($baseurl, 'qo', $orderby);
	$baseurl = querystrings($baseurl, 'qp', $pages);
	$baseurl = querystrings($baseurl, 'qr', $rows);
	$baseurl = querystrings($baseurl, 'qc', $categories);
	$urlaction = querystrings($baseurl, 'qs', $sort);
	
	//action: edit the quote
	if ( $action == 'edit' ) {

		?><div class="wrap"><h2><?php _e('Edit quote '.$quoteID, 'stray-quotes') ?></h2><?php 
	
		//check if something went wrong with quote id
		if ( empty($quoteID) ) {
			?><div id="message" class="error"><p><?php _e('Something is wrong. No quote ID from the query string.','stray-quotes') ?></p></div><?php
		}
		
		else {			
			
			//query
			$data = $wpdb->get_results("select * from " . WP_STRAY_QUOTES_TABLE . " where quoteID='" . mysql_real_escape_string($quoteID) . "' limit 1");
			
			//bad feedback
			if ( empty($data) ) {
				?><div id="message" class="error"><p><?php _e('Something is wrong. I can\'t find a quote linked up with that ID.','stray-quotes') ?></p></div><?php
				return;
			}
			$data = $data[0];
			
			//encode strings
			if ( !empty($data) ) $quote = htmlspecialchars($data->quote); 
			if ( !empty($data) ) $author = htmlspecialchars($data->author);
			if ( !empty($data) ) $source = htmlspecialchars($data->source);
			if ( !empty($data) ) $category = htmlspecialchars($data->category);
			
			//set visibility
			$defaultVisible = get_option ('stray_quotes_default_visible');
			if ( empty($data)){				
				if  ($defaultVisible == 'Y') {			
					$visible_yes = "checked";
					$visible_no = "";
				}
				else {
					$visible_yes = "";
					$visible_no = "checked";				
				}				
			}
			else {			
				if ( $data->visible=='yes' ) {
					$visible_yes = "checked";
					$visible_no = "";
				}
				else {
					$visible_yes = "";
					$visible_no = "checked";				
				}		
			}			
			
			//make the edit form
			$styleborder = 'style="border:1px solid #ccc"';
			$styletextarea = 'style="border:1px solid #ccc; font-family: Times New Roman, Times, serif; font-size: 1.4em;"'; ?>
            <div style="width:42em">
			<script src="<?php echo WP_STRAY_QUOTES_PATH ?>inc/stray_quicktags.js" type="text/javascript"></script>
            <form name="quoteform" id="quoteform" method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
				<input type="hidden" name="qa" value="edit_save">
				<input type="hidden" name="qi" value="<?php echo $quoteID; ?>">
			
				<p><!--<label><?php _e('Quote:','stray-quotes') ?></label><br />-->
                <div style="float:left"><script type="text/javascript">edToolbar();</script></div>
                <div style="float:right; display:compact;margin-top:12px"><small><?php _e('To insert this quote in a post use:','stray-quotes'); ?> <code>[quote id=<?php echo $quoteID ?>]</code></small></div>
                <textarea id="qeditor" name="quote_quote" <?php echo $styletextarea ?> cols=68 rows=7><?php echo $quote; ?></textarea></p>
				<script type="text/javascript">var edCanvas = document.getElementById('qeditor');</script>
                <p class="setting-description"><small><?php _e('* Other than the few offered in the toolbar above, many HTML and non-HTML formatting elements can be used for the quote. Lines can be broken traditionally or using <code>&lt;br/&gt;</code>, etcetera.','stray-quotes'); ?></small></p></p>
                
				<p><label><?php _e('Author:','stray-quotes') ?></label>
                <input type="text" id="aeditor" name="quote_author" size=58 value="<?php echo $author ?>" <?php echo $styleborder ?> />
				<script type="text/javascript">edToolbar1();</script>
                <script type="text/javascript">var edCanvas1 = document.getElementById('aeditor');</script><br />
				
				<label><?php _e('Source:','stray-quotes') ?></label>
                <input type="text" id="seditor" name="quote_source" size=58 value="<?php echo $source ?>" <?php echo $styleborder ?> />
				<script type="text/javascript">edToolbar2();</script>
                <script type="text/javascript">var edCanvas2 = document.getElementById('seditor');</script>
                <p class="setting-description"><small><?php _e('* By adding a link to the author or the source, the default links specified on the settings page are ignored. Make sure the link is closed by a <code>&lt;/a&gt;</code> tag.','stray-quotes'); ?></small></p></p>
                
                <p><label><?php _e('Category:&nbsp;','stray-quotes'); ?></label>                
                <select name="categories" style="vertical-align:middle; width:14em;"> 
                <?php $categorylist = make_categories($current_user->user_nicename); 
                foreach($categorylist as $categoryo){ ?>
                <option value="<?php echo $categoryo; ?>" style=" padding-right:5px"
                <?php  if ( $categoryo == $category) echo ' selected'; ?> >
                <?php echo $categoryo;?></option>
                <?php } ?>   
                </select>
                  
                <label><?php _e('new category:&nbsp;','stray-quotes') ?></label>
                <input type="text" name="quote_category" size=24 value="" <?php echo $styleborder ?> /></p>
                
				<p><label><?php _e('Visible:','stray-quotes') ?></label>
					<input type="radio" name="quote_visible" class="input" value="yes"<?php echo $visible_yes ?> /> <?php _e('Yes','stray-quotes') ?>					
					<input type="radio" name="quote_visible" class="input" value="no"<?php echo $visible_no ?> /> <?php _e('No','stray-quotes') ?>
				</p><p>&nbsp;</p>
				<p> <a href=" <?php echo $urlaction ?>"><?php _e('Cancel','stray-quotes') ?></a>&nbsp;
         	   <input type="submit" name="save"  class="button-primary" value="<?php _e('Update quote','stray-quotes') ?> &raquo;" /></p>
			</form><p>&nbsp;</p></div><?php 
	
		}	
		
	} else { //this "else" separates the edit form from the list of quotes. make it a "else if" below to revert to the old ways
	
		?><div class="wrap">
        <h2><?php _e('Manage quotes','stray-quotes') ?></h2><?php 
		
		$nothingmessage = __('Please select something first.','stray-quotes');
		$wrongmessage = __('Something went wrong.','stray-quotes');
	
		//action: save the quote
		if ( $action == 'edit_save' ) {
		
			//assign variables, trim, replace spaces
			$quote = !empty($_REQUEST['quote_quote']) ? trim($_REQUEST['quote_quote']) : '';	
			$author = !empty($_REQUEST['quote_author']) ? trim($_REQUEST['quote_author']) : '';
			$source = !empty($_REQUEST['quote_source']) ? trim($_REQUEST['quote_source']) : '';
			$visible = !empty($_REQUEST['quote_visible']) ? trim($_REQUEST['quote_visible']) : '';
			if ($_REQUEST['quote_category'])$category = trim($_REQUEST['quote_category']);
			else $category = $_REQUEST['categories'];
			
			if (preg_match('/\s+/',$category)>0){
				$category=preg_replace('/\s+/','-',$category);
				$plusmessage = "<br/>Note: <strong>The name of the category you created contained spaces</strong>, which are not allowed. <strong>I replaced them with dashes</strong>. I hope it's okay.";
			} 
	
			//magic quotes
			if ( ini_get('magic_quotes_gpc') )	{
			
				$quote = stripslashes($quote);
				$author = stripslashes($author);
				$source = stripslashes($source);
				$category = stripslashes($category);
				$visible = stripslashes($visible);	
			}
			
			//negative feedback or UPDATE
			if ( empty($quoteID) )	{
				?><div id="message" class="error fade"><p><?php _e('<strong>Failure:</strong> No quote ID given.','stray-quotes') ?></p></div><?php
			}
			
			else {		
				//update the quote
				$sql = "UPDATE " . WP_STRAY_QUOTES_TABLE 
				. " SET `quote`='" . mysql_real_escape_string($quote)
				. "', `author`='" . mysql_real_escape_string($author) 
				. "', `source`='" . mysql_real_escape_string($source) 
				. "', `category`='" . mysql_real_escape_string($category)
				. "', `visible`='" . mysql_real_escape_string($visible) 
				. "', `user`='" . mysql_real_escape_string($current_user->user_nicename)
				. "' WHERE `quoteID`='" . mysql_real_escape_string($quoteID) . "'";		     
				$wpdb->get_results($sql);
				
				//verify what has been udpated
				$sql = "SELECT `quoteID` FROM " . WP_STRAY_QUOTES_TABLE 
				. " WHERE `quote`='" . mysql_real_escape_string($quote) 
				. "' AND `author`='" . mysql_real_escape_string($author) 
				. "' AND `source`='" . mysql_real_escape_string($source) 
				. "' AND `category`='" . mysql_real_escape_string($category) 
				. "' AND `visible`='" . mysql_real_escape_string($visible) 
				. "' AND `user`='" . mysql_real_escape_string($current_user->user_nicename)
				. "' LIMIT 1";
				$result = $wpdb->get_results($sql);
				
				//feedback
				if ( empty($result) || empty($result[0]->quoteID) )	{			
					?><div id="message" class="error fade"><?php echo $wrongmessage ?></div><?php				
				}
				else {			
					?><div id="message" class="updated fade"><p>
					<?php echo str_replace("%s",$quoteID,__('Quote <strong>%s</strong> updated.'.$plusmessage,'stray-quotes'));?></p></div><?php
				}		
			}
		}
		
		//action: delete quote
		else if ( $action == 'delete' ) {
		
			$sql = "delete from `" . WP_STRAY_QUOTES_TABLE . "` where quoteID='" . mysql_real_escape_string($quoteID) . "'";
			$wpdb->get_results($sql);
			
			$sql = "select quoteID from `" . WP_STRAY_QUOTES_TABLE . "` where quoteID='" . mysql_real_escape_string($quoteID) . "'";
			$result = $wpdb->get_results($sql);
			
			if ( empty($result) || empty($result[0]->quoteID) )	{			
				?><div class="updated"><p><?php echo str_replace("%s",$quoteID,__('Quote <strong>%s</strong> deleted.','stray-quotes')); ?></p></div><?php
			}			
			else {						
				?><div class="error fade"><?php echo $wrongmessage ?></div><?php	
			}		
		}

		//bulk action: delete
		else if ( $_POST['bulk'] == 'multidelete' ) {
		
			$deleteIds = '';
			$count = 0;
			foreach($_POST as $key => $val){
					
				if ( substr($key,0,12) == 'check_select' ) {
					$deleteIds .= "'". $val . "',";
					$count++;
				} 
			}

			$deleteIds = rtrim($deleteIds,',');			
			$sql = "DELETE FROM `" . WP_STRAY_QUOTES_TABLE . "` WHERE `quoteID` IN(".$deleteIds.")";
			$wpdb->query($sql);
			
			$sql2 = "SELECT `quoteID` FROM `" . WP_STRAY_QUOTES_TABLE . "` WHERE `quoteID` IN(".$deleteIds.")";
			$result = $wpdb->get_results($sql2);			
			
			if ($count == 0) { ?><div class="error fade"><?php echo $nothingmessage ?></div><?php
			} else {
				
				if ( empty($result) || empty($result[0]->quoteID) )	{			
					?><div class="updated"><p><?php echo str_replace("%s",$count,__('<strong>%s</strong> quotes deleted.','stray-quotes')); ?></p></div><?php
				} else { ?><div class="error fade"><?php echo $wrongmessage ?></div><?php	}
			}
		}
		
		//bulk action: toggle visibility
		else if ( $_POST['bulk'] == 'togglevisible' ) {
			
			$toggleyes ='';
			$toggleno ='';
			$count = 0;
			foreach($_POST as $key => $val){
					
				if ( substr($key,0,12) == 'check_select' ) {
					
					$sql = "SELECT `visible` FROM `" . WP_STRAY_QUOTES_TABLE . "` WHERE `quoteID`='".$val."'";
					$visibility = $wpdb->get_var($sql);
					if ($visibility=='yes') $toggleyes .= "'". $val . "',";
					else $toggleno .= "'". $val . "',";
					
					$count++;
				}
			}
			
			$toggleyes = rtrim($toggleyes,',');
			$toggleno = rtrim($toggleno,',');
			
			$sql = "UPDATE `" . WP_STRAY_QUOTES_TABLE . "` SET `visible`= 'no' WHERE `quoteID` IN(".$toggleyes.")";
			$wpdb->query($sql);
			$sql1 = "UPDATE `" . WP_STRAY_QUOTES_TABLE . "` SET `visible`= 'yes' WHERE `quoteID` IN(".$toggleno.")";
			$wpdb->query($sql1);

			$sql2 = "SELECT `quoteID` FROM `" . WP_STRAY_QUOTES_TABLE . "` WHERE `visible` IN(".$toggleyes.") = 'no'";
			$result1 = $wpdb->get_results($sql2);
			$sql3 = "SELECT `quoteID` FROM `" . WP_STRAY_QUOTES_TABLE . "` WHERE `visible` IN(".$toggleno.") = 'yes'";
			$result2 = $wpdb->get_results($sql3);
			
			if ($count == 0) { ?><div class="error fade"><?php echo $nothingmessage ?></div><?php
			} else {

				if ( $result1 || $result2 )	{			
					?><div class="updated"><p><?php echo str_replace("%s",$count,__('Visibility toggled for <strong>%s</strong> quotes.','stray-quotes')); ?></p></div><?php
				}			
				else {						
					?><div class="error fade"><?php echo $wrongmessage ?></div><?php	
				}	
			}
		}

		//bulk action: change category
		else if ( $_POST['bulk'] == 'changecategory' ) {
			
			$newcat = $_POST['catselect'];
			$catlist = '';
			$count = 0;
			foreach($_POST as $key => $val){
					
				if ( substr($key,0,12) == 'check_select' ) {
					$catlist .= "'". $val . "',";
					$count++;
				}
			}

			$catlist = rtrim($catlist,',');			
			$sql = "UPDATE `" . WP_STRAY_QUOTES_TABLE . "` SET `category`='".$newcat."' WHERE `quoteID` IN(".$catlist.")";
			$wpdb->query($sql);

			$sql2 = "SELECT DISTINCT `category` FROM `" . WP_STRAY_QUOTES_TABLE . "` WHERE `quoteID` IN(".$catlist.")";
			$result = $wpdb->get_var($sql2);			

			if ($count == 0) { ?><div class="error fade"><?php echo $nothingmessage ?></div><?php
			} else {

				if ( $result == $newcat )	{			
					?><div class="updated"><p><?php echo str_replace("%s",$count,__('Category changed for <strong>%s</strong> quotes.','stray-quotes')); ?></p></div><?php
				}			
				else {						
					?><div class="error fade"><?php echo $wrongmessage ?></div><?php	
				}	
			}
		}	
		
		//bulk action: no action
		else if ( $_POST['bulk'] == 'noaction' ){ 
		
		 ?><div class="error fade"><?php _e('Please select something in the \'Bulk Actions\' menu first.','stray-quotes'); ?></div><?php 
		}

		// prepares WHERE condition (categories/users)
		$where = '';
		if (!$categories || $categories == 'all') {
			
			if(!current_user_can('manage_options'))$where = " WHERE `user`='" . $current_user->user_nicename . "'";
			else $where = '';
			
		} else {
			
			if(!current_user_can('manage_options'))$where = " WHERE `category`='" . $categories . "' AND `user`='" . $current_user->user_nicename . "'";
			else $where = " WHERE `category`='" . $categories . "'";
			
		}
			
		
		// how many rows we have in database
		$numrows = $wpdb->get_var("SELECT COUNT(`quoteID`) as rows FROM " . WP_STRAY_QUOTES_TABLE . $where);
		
		//temporary workaround for the "division by zero" problem
		if (is_string($rows))$rows=intval($rows);
		settype($rows, "integer"); 
		
		// how many pages we have when using paging?
		if ($rows == NULL || $rows < 10) $rows = 10; 
		$maxPage = ceil($numrows/$rows);		
		
		// print the link to access each page (thanks to http://www.php-mysql-tutorial.com/wikis/php-tutorial/paging-using-php.aspx)
		$nav  = '';
		for($quotepage = 1; $quotepage <= $maxPage; $quotepage++) {
			
			//with few pages, print all the links
			if ($maxPage < 4) {
				
				if ($quotepage == $pages)$nav .= $quotepage; // no need to create a link to current page
				else $nav .= ' <a href="'.querystrings($urlaction, 'qp', $quotepage).'">'.$quotepage.'</a> ';
			
			//with many pages
			} else {
				
				if ($quotepage == $pages)$nav .= $quotepage; // no need to create a link to current page
				else if ($quotepage == 1 || $quotepage == $maxPage)$nav .= ''; //no need to create first and last (they are created by the first and last links afterwards)
				else {
					
					//print links that are close to the current page (< 2 steps away)
					if ( ($quotepage < ($pages+2)) && ($quotepage > ($pages-2)) )$nav .= ' <a href="'.querystrings($urlaction, 'qp', $quotepage).'">'.$quotepage.'</a> ';
					
					//otherwise they're dots
					else {
						
						if ($pages > 3) $fdot = '.';
						if ($pages != ($maxPage-1)) $ldot = '.';
					}
					
				}
				
			}
		   
		}

		//print first and last, next and previous links
		if ($pages > 1) {
			
			$quotepage  = $pages - 1;		
			$prev  = ' <a href="'.querystrings($urlaction, 'qp', $quotepage).'" title="Previous '.$rows.'">&laquo;</a> ';		
			if ($maxPage > 4) $first = ' <a href="'.querystrings($urlaction, 'qp', '1').'">1</a> '.$fdot.' ';
		}
		else {
		   $prev  = '&nbsp;'; // we're on page one, don't print previous link
		   if ($maxPage > 4) $first = '&nbsp;';  //nor the first page link
		}
		
		if ($pages < $maxPage) {
		
			$missing = $numrows-($rows*$pages);		
			if ($missing > $rows) $missing = $rows;
			
			$quotepage = $pages + 1;
			$next = ' <a href="'.querystrings($urlaction, 'qp', $quotepage).'" title=" Next '.$missing.'">&raquo;</a> ';
			if ($maxPage > 4) $last = ' ' .$ldot.' <a href="'.querystrings($urlaction, 'qp', $maxPage).'"> '.$maxPage.'</a> ';
		}
		else {
		   $next = '&nbsp;'; // we're on the last page, don't print next link
		   if ($maxPage > 4) $last = '&nbsp;';  //nor the last page link
		}		
	
		//get all the quotes
		$sql = "SELECT `quoteID`,`quote`,`author`,`source`,`category`,`visible`, `user` FROM " 
		. WP_STRAY_QUOTES_TABLE 
		. $where
		. " ORDER BY `". $orderby ."`"
		. $sort 
		. " LIMIT " . $offset. ", ". $rows;
		
		$quotes = $wpdb->get_results($sql);
		
		//page number has to be reset to 1 otherwise it would look like you have no quotes left when you are on a page too high for so many quotes.
		$urlrows = querystrings($urlaction, 'qp', '1');
	
		//HTML		
		$bulkurl = remove_querystring_var($_SERVER['REQUEST_URI'], 'qa');
		$bulkurl = remove_querystring_var($bulkurl, 'qi');
		?><form name="bulkform" id="bulkform" method="post" action="<?php echo $bulkurl ?>">
        <div class="tablenav">
        <div class="alignleft actions" style="margin-right:10px">      
		<select name="bulk" id="bulkselect" style="vertical-align:middle;max-width:110px" onchange="javascript:disable_enable()" />
		<option value="noaction" ><?php _e('Bulk Actions','stray-quotes'); ?></option>
		<option value="multidelete"><?php _e('delete','stray-quotes'); ?></option>
		<option value="togglevisible"><?php _e('toggle visibility','stray-quotes'); ?></option>
		<option value="changecategory"><?php _e('change category','stray-quotes'); ?></option>
		</select>
		<select name="catselect" id="catselect" style="vertical-align:middle;max-width:120px"> 
		<?php 
		if(current_user_can('manage_options'))$categorylist = make_categories();
		else $categorylist = make_categories($current_user->user_nicename);
		foreach($categorylist as $categoryo){ 
			?><option value="<?php echo $categoryo; ?>" >
			<?php echo $categoryo;?></option>
		<?php } ?>   
		</select>
		<input type="submit" value="<?php _e('Apply','stray-quotes'); ?>" class="button-secondary action" />
		</div>
        
        <div class="alignleft actions"> 
		<span style="color:#666; font-size:11px;white-space:nowrap;"><?php _e('display ','stray-quotes'); ?> </span>
		<select name="lines" onchange="switchpage(this)"  style="vertical-align:middle">
		<option value=<?php echo querystrings($urlrows, 'qr', '10'); if ( $rows == 10) echo ' selected';  ?> ><?php _e('10 quotes', 'stray-quotes'); ?></option>
		<option value=<?php echo querystrings($urlrows, 'qr', '15'); if ( $rows == 15) echo ' selected'; ?> ><?php _e('15 quotes', 'stray-quotes'); ?></option>
		<option value=<?php echo querystrings($urlrows, 'qr', '20'); if ( $rows == 20) echo ' selected'; ?> ><?php _e('20 quotes', 'stray-quotes'); ?></option>
		<option value=<?php echo querystrings($urlrows, 'qr', '30'); if ( $rows == 30) echo ' selected'; ?> ><?php _e('30 quotes', 'stray-quotes'); ?></option>
		<option value=<?php echo querystrings($urlrows, 'qr', '50'); if ( $rows == 50) echo ' selected'; ?> ><?php _e('50 quotes', 'stray-quotes'); ?></option>
		<option value=<?php echo querystrings($urlrows, 'qr', '100'); if ( $rows == 100) echo ' selected'; ?> ><?php _e('100 quotes', 'stray-quotes'); ?></option>
		</select> <!--<span style="color:#666; font-size:11px;white-space:nowrap;"><?php _e(' from ','stray-quotes'); ?> </span>-->
		<select name="categories" onchange="switchpage(this)"  style="vertical-align:middle;max-width:120px"> 
		<option value="<?php echo querystrings($urlaction, 'qc', 'all'); ?>" 
		<?php  if ( $categories == '' || $categories == 'all' ) echo ' selected'; ?>><?php _e('all categories','stray-quotes'); ?></option>
		<?php if(current_user_can('manage_options'))$categorylist = make_categories();
		else $categorylist = make_categories($current_user->user_nicename);
		foreach($categorylist as $categoryo){ 
			if (preg_match('/\s+/',$categoryo)>0)$categoryo = preg_replace('/\s+/','-',$categoryo);
			?><option value="<?php echo querystrings($urlaction, 'qc', $categoryo); ?>" <?php  if ( $categories) {if ( $categories == $categoryo) echo ' selected';} ?> ><?php echo $categoryo;?></option>
		<?php } ?>   
		</select></div>	
        
		<div class="tablenav-pages">
		<?php $search = array("%s1", "%s2");
		$replace = array($pages,$maxPage);		
		echo str_replace($search,$replace,__('<span class="displaying-num">Page %s1 of %s2</span>', 'stray-quotes')); 
		echo '<strong>'.  $prev .$first . $nav . $last . $next . '</strong>'; ?></div>
		</div>
		<?php
		
		//build table
		if ( !empty($quotes) ) {
			$imgasc = WP_STRAY_QUOTES_PATH . 'img/s_asc.png';
			$imgdsc = WP_STRAY_QUOTES_PATH . 'img/s_desc.png';
			?><table class="widefat" id="straymanage">
            
            <?php //column headers ?>
			<thead><tr>
				<th scope="col" style="padding-left: 0; margin-left:0">
				<input type="checkbox" style="padding-left:0" /></th>   
				
				<th scope="col" style="white-space: nowrap;"> <?php if ($numrows != 1) { if ( $orderby != 'quoteID') { ?>
				<a href="<?php echo querystrings($urlaction, 'qo', 'quoteID'); ?>" title="Sort"><?php _e('ID','stray-quotes') ?></a>
				<?php } else { _e('ID','stray-quotes');
					if ($sort == 'ASC') { ?><a href="<?php echo querystrings($urlaction, 'qs', 'DESC'); ?>">
					<img src= <?php echo $imgasc ?> alt="Descending" title="Descending" /></a> <?php }
					else if ($sort == 'DESC') { ?><a href="<?php echo querystrings($urlaction, 'qs', 'ASC'); ?>">
					<img src= <?php echo $imgdsc ?> alt="Ascending" title="Ascending" /></a> <?php } ?>
							
				<?php } }else{ _e('ID','stray-quotes'); }?>            
				</th>
				
				<th scope="col"> <?php _e('Quote','stray-quotes') ?> </th>
				
				<th scope="col" style="white-space: nowrap;"> <?php if ($numrows != 1) { if ( $orderby != 'author') { ?>
				<a href="<?php echo querystrings($urlaction, 'qo', 'author'); ?>"><?php _e('Author','stray-quotes') ?></a>
				<?php } else { _e('Author','stray-quotes');
					if ($sort == 'ASC') { ?><a href="<?php echo querystrings($urlaction, 'qs', 'DESC'); ?>">
					<img src= <?php echo $imgasc ?> alt="Descending" title="Descending" /></a> <?php }
					else if ($sort == 'DESC') { ?><a href="<?php echo querystrings($urlaction, 'qs', 'ASC'); ?>">
					<img src= <?php echo $imgdsc ?> alt="Ascending" title="Ascending" /></a> <?php } ?>
				<?php } }else{ _e('Author','stray-quotes'); } ?>            
				</th>
				
				<th scope="col" style="white-space: nowrap;"> <?php if ($numrows != 1) { if ( $orderby != 'source') { ?>
				<a href="<?php  echo querystrings($urlaction, 'qo', 'source'); ?>"><?php _e('Source','stray-quotes') ?></a>
				<?php } else { _e('Source','stray-quotes');
					if ($sort == 'ASC') { ?><a href="<?php echo querystrings($urlaction, 'qs', 'DESC'); ?>">
					<img src= <?php echo $imgasc ?> alt="Descending" title="Descending" /></a> <?php }
					else if ($sort == 'DESC') { ?><a href="<?php echo querystrings($urlaction, 'qs', 'ASC'); ?>">
					<img src= <?php echo $imgdsc ?> alt="Ascending" title="Ascending" /></a> <?php } ?>
				<?php }}else{ _e('Source','stray-quotes'); }  ?>            
				</th>
				
				<th scope="col" style="white-space: nowrap;"> <?php if ($numrows != 1) { if ( $orderby != 'category') { ?>
				<a href="<?php  echo querystrings($urlaction, 'qo', 'category'); ?>"><?php _e('Category','stray-quotes') ?></a>
				<?php } else { _e('Category','stray-quotes');
					if ($sort == 'ASC') { ?><a href="<?php echo querystrings($urlaction, 'qs', 'DESC'); ?>">
					<img src= <?php echo $imgasc ?> alt="Descending" title="Descending" /></a> <?php }
					else if ($sort == 'DESC') { ?><a href="<?php echo querystrings($urlaction, 'qs', 'ASC'); ?>">
					<img src= <?php echo $imgdsc ?> alt="Ascending" title="Ascending" /></a> <?php } ?>
				<?php } }else{ _e('Category','stray-quotes'); } ?>            
				</th>
				
				<th scope="col" style="white-space: nowrap;"> <?php if ($numrows != 1) { if ( $orderby != 'visible') { ?>
				<a href="<?php  echo querystrings($urlaction, 'qo', 'visible'); ?>"><?php _e('Visible','stray-quotes') ?></a>
				<?php } else { _e('Visible','stray-quotes');
					if ($sort == 'ASC') { ?><a href="<?php echo querystrings($urlaction, 'qs', 'DESC'); ?>">
					<img src= <?php echo $imgasc ?> alt="Descending" title="Descending" /></a> <?php }
					else if ($sort == 'DESC') { ?><a href="<?php echo querystrings($urlaction, 'qs', 'ASC'); ?>">
					<img src= <?php echo $imgdsc ?> alt="Ascending" title="Ascending" /></a> <?php } ?>
				<?php }}else{ _e('Visible','stray-quotes'); }  ?>            
				</th>
                
				<th scope="col">&nbsp;</th>
				<th scope="col">&nbsp;</th>

				<?php if(current_user_can('manage_options') && $quotesoptions['stray_multiuser'] == true) { ?>
                <th scope="col" style="white-space: nowrap;"> <?php if ($numrows != 1) { if ( $orderby != 'user') { ?>
				<a href="<?php  echo querystrings($urlaction, 'qo', 'user'); ?>"><?php _e('User','stray-quotes') ?></a>
				<?php } else { _e('User','stray-quotes');
					if ($sort == 'ASC') { ?><a href="<?php echo querystrings($urlaction, 'qs', 'DESC'); ?>">
					<img src= <?php echo $imgasc ?> alt="Descending" title="Descending" /></a> <?php }
					else if ($sort == 'DESC') { ?><a href="<?php echo querystrings($urlaction, 'qs', 'ASC'); ?>">
					<img src= <?php echo $imgdsc ?> alt="Ascending" title="Ascending" /></a> <?php } ?>
				<?php }}else{ _e('User','stray-quotes'); }  ?>            
				</th><?php } ?>
				
                
			</tr></thead>
                
            <?php //table rows ?>
            <tbody><?php
			
			$i = 0;	
			foreach ( $quotes as $quote ) {
			
				$alt = ($i % 2 == 0) ? ' class="alternate"' : ''; ?>		
				<tr <?php echo($alt); ?> <?php if( $quote->user != $current_user->user_nicename ) echo ' style="color:#aaa"' ?> >
                				
					<td scope="col" style="white-space: nowrap;"><input type="checkbox" name="<?php echo 'check_select'.$i ?>" value="<?php echo $quote->quoteID ?>" /> </td> 
					
					<th scope="row"><?php echo ($quote->quoteID); ?></th>
					<td><?php echo(nl2br($quote->quote)); ?></td>
					<td><?php echo($quote->author); ?></td>
					<td><?php echo($quote->source); ?></td>
					<td><?php if ($quote->category == 'default')echo('<em>'.$quote->category.'</em>'); else echo $quote->category;?></td>
					<td><?php if( $quote->visible == 'yes' ) _e( 'yes', 'stray-quotes' ); else _e( 'no', 'stray-quotes' ); ?></td>
										
					<td align="center">
					<a href="<?php echo querystrings( querystrings($urlaction, 'qa', 'edit'), 'qi', $quote->quoteID ); ?>">
					<?php _e('Edit','stray-quotes') ?></a></td>
	
					<td align="center">
					<a href="
					<?php echo querystrings( querystrings($urlaction, 'qa', 'delete'), 'qi', $quote->quoteID );  ?>"
					onclick="if ( confirm('<?php echo __( 'You are about to delete quote ','stray-quotes') . $quote->quoteID . '.\\n\\\'' . __('Cancel','stray-quotes') . '\\\' ' . __('to stop','stray-quotes') . ', \\\'OK\\\' ' . __('to delete','stray-quotes') . '.\''; ?>) ) { return true;}return false;"><?php echo __('Delete','stray-quotes') ?></a></td>			
                    
                    <?php if(current_user_can('manage_options') && $quotesoptions['stray_multiuser'] == true) { ?>
					<td><?php if( $quote->user == $current_user->user_nicename )echo ''; else echo $quote->user; ?></td>
                    <?php } ?>
                    
				</tr>
				<?php $i++; 
			} ?>
			</tbody>
            
            <?php //end table and navigation ?>
            </table><div class="tablenav"><div class="tablenav-pages">
			<?php $search = array("%s1", "%s2");
            $replace = array($pages,$maxPage);		
            echo str_replace($search,$replace,__('<span class="displaying-num">Page %s1 of %s2</span>', 'stray-quotes')); 
            echo '<strong>'.  $prev .$first . $nav . $last . $next . '</strong>'; ?>
            </div></div></form><?php
			
		} else { ?><p><div style="clear:both"> <?php echo str_replace("%s1",get_option('siteurl')."/wp-admin/admin.php?page=stray_manage",__('<br/>No quotes here. Maybe you want to <a href="%s1">reopen</a> this page.','stray-quotes')); ?> </div></p>
		</div><?php	}	
	}

?></div><?php

}


?>