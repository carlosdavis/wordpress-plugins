<?php 

//options page
function stray_quotes_options() {
	
	global $wpdb;
	
	//check whether user can manage options
	if(!current_user_can('manage_options'))die('Access Denied');	
	
	//decode and intercept
	foreach($_POST as $key => $val) {
		$_POST[$key] = stripslashes(utf8_encode($val));
	}
	
	//handle the post event
	if(!empty($_POST['do'])) {

		// check URLs
		if ($_POST['link_to'] == 'http://') unset($_POST['link_to']);				
		/*else if (false === strpos($_POST['link_to'],'%AUTHOR%')) {  
			unset($_POST['link_to']);
			$msgvar1 = 1;
		} */		
		else if ( !preg_match("#((http|https|ftp)://(\S*?\.\S*?))(\s|\;|\)|\]|\[|\{|\}|,|\"|'|:|\<|$|\.\s)#ie",$_POST['link_to'])		
		/*( false === filter_var($_POST['link_to'], FILTER_VALIDATE_URL)*/){
			unset($_POST['link_to']);
			$msgvar3 = 1;
		}				
		if ($_POST['source_link_to'] == 'http://') unset($_POST['source_link_to']);				
		/*else if (false === strpos($_POST['source_link_to'],'%SOURCE%')) {  
			unset($_POST['source_link_to']);
			$msgvar2 = 1;
		} */		
		else if ( !preg_match("#((http|https|ftp)://(\S*?\.\S*?))(\s|\;|\)|\]|\[|\{|\}|,|\"|'|:|\<|$|\.\s)#ie",$_POST['source_link_to'])){
			unset($_POST['source_link_to']);
			$msgvar3 = 1;
		}
		
		//check loader
		if ($_POST['loader'])$_POST['loader'] = strip_tags($_POST['loader']);
		
		//get existing options
		$existingoptions = get_option('stray_quotes_options');
		
		//create array of values *ALL VALUES MUST BE INCLUDED HERE
		$quotesoptions = array(	
							   
		'stray_quotes_before_all' => $_POST['before_all'],		
		'stray_quotes_after_all' => $_POST['after_all'],
		'stray_quotes_before_quote' => $_POST['before_quote'],	
		'stray_quotes_after_quote' => $_POST['after_quote'],
		'stray_quotes_before_author' => $_POST['before_author'],	
		'stray_quotes_after_author' => $_POST['after_author'],
		'stray_quotes_before_source' => $_POST['before_source'],	
		'stray_quotes_after_source' => $_POST['after_source'],
		'stray_quotes_put_quotes_first' => $_POST['put_quotes_first'],
		'stray_quotes_default_visible' => $_POST['default_visible'],
		'stray_quotes_linkto' => $_POST['link_to'],	
		'stray_quotes_sourcelinkto' => $_POST['source_link_to'],	
		'stray_quotes_authorspaces' => $_POST['author_spaces'],	
		'stray_quotes_sourcespaces' => $_POST['source_spaces'],	
		'stray_quotes_order' => $_POST['order'],
		'stray_quotes_rows' => $_POST['rows'],
		'stray_quotes_categories' => $_POST['categories'],
		'stray_quotes_sort' => $_POST['sort'],
		'stray_default_category' => $_POST['default_category'],
		'stray_if_no_author'=> $_POST['no_author'],	
		'stray_clear_form'=> $_POST['clear_form'],
		'stray_ajax'=> $_POST['stray_ajax'],
		'stray_before_loader'=> $_POST['before_loader'],
		'stray_loader'=> $_POST['loader'],
		'stray_after_loader'=> $_POST['after_loader'],
		'stray_loading'=> $_POST['stray_loading'],
		'stray_multiuser'=> $_POST['stray_multiuser'],
		
		'comment_scode'=> $existingoptions['comment_scode'],
		'title_scode'=> $existingoptions['title_scode'],
		'excerpt_scode'=> $existingoptions['excerpt_scode'],
		'widget_scode'=> $existingoptions['widget_scode'],
		'categories_scode'=> $existingoptions['categories_scode'],
		'tags_scode'=> $existingoptions['tags_scode'],
		'bloginfo_scode'=> $existingoptions['bloginfo_scode'],
		'bookmarlet_source'=> $existingoptions['bookmarlet_source'],
		'bookmarklet_cat'=> $existingoptions['bookmarklet_cat']
		
		);		
		
		//update options
		$update_quotes_options = update_option('stray_quotes_options', $quotesoptions);			
		
		if ($update_quotes_options) {
        
            //positive feedback
             ?><div id="message" class="updated fade below-h2"><p>
            <?php _e('<strong>Options saved...</strong> ','stray-quotes');
            if ( $msgvar3 == 1) _e('No problems. Well, except for one or more invalid urls that I discarded.', 'stray-quotes');
            /*else if ($msgvar1 == 1) _e('No problems. Well, except that there was no variable in the author link. I discared it.', 'stray-quotes');
            else if ($msgvar2 == 1) _e('No problems. Well,  except that there was no variable in the source link. I discared it.', 'stray-quotes');*/
            else _e('No problems.', 'stray-quotes'); ?></p></div><?php 
		
		} else {
		
			//negative feedback		
			?><div id="message" class="error fade below-h2"><p>
            <?php if ( $msgvar3 == 1 /*|| $msgvar1 == 1|| $msgvar2 == 1*/) 
				_e('The operation failed because of one or more invalid urls that I discarded.', 'stray-quotes');
			else 
				_e('<strong>The options could not be saved</strong>. Either the operation went wrong, or you didn\'t make any changes.</strong> ','stray-quotes'); 
			?></p></div><?php 
		}			
		
	}	
	
	//get the options
	$quotesoptions = array();
	$quotesoptions = get_option('stray_quotes_options');
	$regularTitle =  $quotesoptions['stray_quotes_regular_title'];
	$widgetTitle = $quotesoptions['stray_quotes_widget_title'];
	$beforeAll =  $quotesoptions['stray_quotes_before_all'];
	$afterAll = $quotesoptions['stray_quotes_after_all'];
	$beforeQuote = $quotesoptions['stray_quotes_before_quote'];
	$afterQuote = $quotesoptions['stray_quotes_after_quote'];
	$beforeAuthor = $quotesoptions['stray_quotes_before_author'];
	$afterAuthor = $quotesoptions['stray_quotes_after_author'];
	$beforeSource = $quotesoptions['stray_quotes_before_source'];
	$afterSource = $quotesoptions['stray_quotes_after_source'];
	$putQuotesFirst = $quotesoptions['stray_quotes_put_quotes_first'];
	$defaultVisible = $quotesoptions['stray_quotes_default_visible'];
	$linkto = $quotesoptions['stray_quotes_linkto'];
	$sourcelinkto = $quotesoptions['stray_quotes_sourcelinkto'];
	$sourcespaces = $quotesoptions['stray_quotes_sourcespaces'];	
	$authorspaces = $quotesoptions['stray_quotes_authorspaces'];	
	$order = $quotesoptions['stray_quotes_order'];
	$rows = $quotesoptions['stray_quotes_rows'];
	$categories = $quotesoptions['stray_quotes_categories'];
	$sort = $quotesoptions['stray_quotes_sort'];
	$defaultcategory = $quotesoptions['stray_default_category'];
	$ifnoauthor = $quotesoptions['stray_if_no_author'];	
	$clearform = $quotesoptions['stray_clear_form'];
	$strayajax = $quotesoptions['stray_ajax'];
	$beforeloader = $quotesoptions['stray_before_loader'];
	$quoteloader = $quotesoptions['stray_loader'];
	$afterloader = $quotesoptions['stray_after_loader'];
	$loading = $quotesoptions['stray_loading'];
	$multiuser = $quotesoptions['stray_multiuser'];
	
	if ( $putQuotesFirst == 'Y' ) $putQuotesFirst_selected = 'checked';	
	if ( $defaultVisible == 'Y' ) $defaultVisible_selected = 'checked';	
	if ( $clearform == 'Y' ) $clearform_selected = 'checked';	
	if ( $strayajax == 'Y' )$strayajax_selected = 'checked';
	if ( $multiuser == 'Y' )$multiuser_selected = 'checked';
	
	//javascript to disable ajax forms
	?><script type="text/javascript">
		
		function disablefields() {
			// Get a fieldset
			var a = document.getElementById('ajaxinput1');
			var b = document.getElementById('ajaxinput2');
			var c = document.getElementById('ajaxinput3');
			var d = document.getElementById('ajaxinput4');
			<?php if ( $strayajax == "Y" ) { ?>
			a.disabled = true;
			b.disabled = true;
			c.disabled = true;
			d.disabled = true;
			<?php } else { ?>
			a.disabled = false;
			b.disabled = false;
			c.disabled = false;
			d.disabled = false;
			<?php } ?>
		} </script>	
	
	<?php //the options form	?>
	<form name="frm_options" method="post" action="<?php echo ($_SERVER['REQUEST_URI']); ?>">

    <?php //quote aspect ?>
    <div class="wrap"><h2><?php _e('Settings','stray-quotes'); ?></h2>

    <p><h3 style="line-height:.1em"><?php _e('How the quotes look','stray-quotes') ?></h3>
    <span class="setting-description"><?php _e('Default settings to change how the quotes appear in your blog. These settings apply to ALL widgets, shortcodes or template tags, but they can be disabled from those if needed.','stray-quotes') ?></span>
    </p>
	<table class="form-table"> 
	
	<tr valign="top"><th scope="row"><?php _e('The Quote Area','stray-quotes') ?></th>    
        <td><input type="text" size="50" name="before_all" value="<?php echo (utf8_decode(htmlspecialchars($beforeAll))); ?>"class="regular-text" /><span class="setting-description">
		<?php _e('<br/>HTML or other elements <strong>before the quote area</strong>, which comes after the widget title and includes Quote, Author and Source. If more than one quote is listed, Quote Area is considered the entire list.<br/><strong>Sample value:</strong>','stray-quotes') ?> <code>&lt;div align=&quot;right&quot;&gt;</code></span></td>
        <td><input type="text" size="50" name="after_all" value="<?php echo (utf8_decode(htmlspecialchars($afterAll))); ?>"class="regular-text" /><span class="setting-description">
		<?php _e('<br/>HTML or other elements <strong>after the quote area</strong>.<br/><strong>Sample value:</strong>','stray-quotes') ?> <code>&lt;/div&gt;</code></span>   
	</td></tr>
    
	<tr valign="top" style="background:#F0F0F0"><th scope="row"><?php _e('Quote','stray-quotes') ?></th>    
        <td><input type="text" size="50" name="before_quote" value="<?php echo (utf8_decode(htmlspecialchars($beforeQuote))); ?>"class="regular-text" /><span class="setting-description">
        <?php _e('<br/>HTML or other elements before the quote.<br/><strong>Sample value:</strong>','stray-quotes') ?> <code>&amp;#8220;</code></span>
        <td><input type="text" size="50" name="after_quote" value="<?php echo (utf8_decode(htmlspecialchars($afterQuote))); ?>"class="regular-text" /><span class="setting-description">
		<?php _e('<br/>HTML or other elements after the quote.<br/><strong>Sample value:</strong>','stray-quotes') ?> <code>&amp;#8221;</code></span>    
    </td></tr>
    
	<tr valign="top"><th scope="row"><?php _e('Author','stray-quotes') ?></th><td>    
        <input type="text" size="50" name="before_author" value="<?php echo (utf8_decode(htmlspecialchars($beforeAuthor))); ?>" class="regular-text" /><span class="setting-description">
		<?php _e('<br/>HTML or other elements before the author.<br/><strong>Sample value:</strong>','stray-quotes') ?> <code>&lt;br/&gt;<?php _e('by','stray-quotes'); ?>&amp;nbsp;</code></span>
        <br/>
        <input type="text" size="50" name="after_author" value="<?php echo (utf8_decode(htmlspecialchars($afterAuthor))); ?>" class="regular-text" /><span class="setting-description">
		<?php _e('<br/>HTML or other elements after the author.','stray-quotes') ?></span></td>
        <td><input type="text" size="50" name="link_to" value="<?php if ($linkto) echo (utf8_decode(htmlspecialchars($linkto))); else echo 'http://'; ?>" class="regular-text code" /><span class="setting-description">
		<?php _e('<br/>You can link the Author to a website of your choice.
		<br/>Use this variable in your link: <code>%AUTHOR%</code><br/>
		<strong>Sample values:</strong>','stray-quotes') ?> <code>http://www.google.com/search?q=&quot;%AUTHOR%&quot;</code><br/> 
		<code>http://en.wikipedia.org/wiki/%AUTHOR%</code><br />
        <?php _e('Replace spaces within %AUTHOR% with ','stray-quotes') ?>
        <input type="text" size="1" maxlength="1" name="author_spaces" value="<?php echo (utf8_decode(htmlspecialchars($authorspaces))); ?>" /></span>
   	</td></tr>
    
	<tr valign="top" style="background:#F0F0F0"><th scope="row"><?php _e('Source','stray-quotes') ?></th><td>    
        <input type="text" size="50" name="before_source" value="<?php echo (utf8_decode(htmlspecialchars($beforeSource))); ?>" class="regular-text" /><span class="setting-description">
		<?php _e('<br/>HTML or other elements before the source.<br/><strong>Sample value:</strong>','stray-quotes') ?> <code>,&lt;em&gt;&amp;nbsp;</code></span><br/>
        <input type="text" size="50" name="no_author" value="<?php echo (utf8_decode(htmlspecialchars($ifnoauthor))); ?>" class="regular-text" /><span class="setting-description">
		<?php _e('<br/>HTML or other elements before the source <strong>if there is no author</strong>.<br/>Overrides the field above when no author is present.<br/><strong>Sample value:</strong>','stray-quotes') ?> <code>&lt;br/&gt;source:&amp;nbsp;</code></span>
        <br/>        
        <input type="text" size="50" name="after_source" value="<?php echo (utf8_decode(htmlspecialchars($afterSource))); ?>" class="regular-text" /><span class="setting-description">
		<?php _e('<br/>HTML or other elements after the source.<br/><strong>Sample value:</strong>','stray-quotes') ?> <code>&lt;/em&gt;</code></span></td>
        <td><input type="text" size="50" name="source_link_to" value="<?php if ($sourcelinkto) echo (utf8_decode(htmlspecialchars($sourcelinkto))); else echo 'http://'; ?>" class="regular-text code" /><span class="setting-description">
		<?php _e('<br/>You can link the Source to a website of your choice.
		<br/>Use this variable in your link: <code>%SOURCE%</code><br/>
		<strong>Sample values:</strong>','stray-quotes') ?> <code>http://www.google.com/search?q=&quot;%SOURCE%&quot;</code><br/> 
		<code>http://en.wikipedia.org/wiki/%SOURCE%</code><br />
        <?php _e('Replace spaces within %SOURCE% with ','stray-quotes') ?>
        <input type="text" size="1" maxlength="1" name="source_spaces" value="<?php echo (utf8_decode(htmlspecialchars($sourcespaces))); ?>" />
        </span>
   	</td></tr>
    
	<tr valign="top"><th scope="row"><?php _e('Quote before Author and Source','stray-quotes') ?></th><td colspan="2">    
    	<input type="checkbox" name="put_quotes_first" value="Y" <?php echo ($putQuotesFirst_selected); ?> /><span class="setting-description">
        <?php _e('If checked, returns the quote before author and source (The opposite is NOT considered by the <code>[all-quotes]</code> shortcode).','stray-quotes') ?></span>
    </td></tr>
    
    </table>
    <br/>
   	<div class="submit">
    <input type="hidden" name="do" value="Update" />
    <input type="submit" value="<?php _e('Update all Settings','stray-quotes') ?> &raquo;" />
    </div>
    
   <p>&nbsp;</p>
   
	<?php //AJAX loader ?>
    <p><h3 style="line-height:.1em"><?php _e('AJAX settings','stray-quotes') ?></h3>
    <span class="setting-description"><?php _e('Default settings for the dynamic loader.','stray-quotes') ?></span>
    </p>
    <table class="form-table"> 
      
    <tr valign="top"><th scope="row"><?php _e('Enable/Disable AJAX','stray-quotes') ?></th>    
        <td colspan="2"><input type="checkbox" name="stray_ajax" value="Y" <?php echo ($strayajax_selected); ?> onchange="javascript:disable_enable()" /><span class="setting-description"><?php  echo str_replace("%s",get_option("siteurl").'/wp-admin/admin.php?page=stray_help',__('&nbsp;If checked, <strong>disables the AJAX loader</strong> entirely. If unchecked, AJAX can still be disabled from the widgets, the template tags or the shortcodes (see <a href="%s">help page</a>).','stray-quotes')) ?></span></td></tr>
        
    <tr valign="top" style="background:#F0F0F0"><th scope="row"><?php _e('The AJAX loader','stray-quotes') ?></th>
    <td><input type="text" size="50" name="loader" value="<?php echo (utf8_decode(htmlspecialchars($quoteloader))); ?>" class="regular-text" id="ajaxinput1" /><span class="setting-description"><?php echo str_replace("%s",get_option("siteurl").'/wp-admin/admin.php?page=stray_help',__('<br/>Enter here the <strong>link phrase</strong> used to dynamically load another quote. HTML not allowed. If you leave this empty, the quote will be reloaded by clicking on it. You can <strong>override</strong> this setting in widgets, shortcodes or in the template (see <a href="%s">help page</a>), <strong>only</strong> if you leave this field empty.<br/><strong>Sample value:</strong>','stray-quotes')) ?> <code>New quote &amp;raquo;</code></span></td>        
    <td><input type="text" size="50" name="before_loader" value="<?php echo (utf8_decode(htmlspecialchars($beforeloader))); ?>" class="regular-text"  id="ajaxinput3" /><span class="setting-description"><?php _e('<br/>HTML or other elements before the quote loader.<br/><strong>Sample value:</strong>','stray-quotes') ?> <code>&lt;p align="left"&gt;</code></span><br/> 
    <input type="text" size="50" name="after_loader" value="<?php echo (utf8_decode(htmlspecialchars($afterloader))); ?>" class="regular-text"  id="ajaxinput4"  /><span class="setting-description"><?php _e('<br/>HTML or other elements after the quote loader.<br/><strong>Sample value:</strong>','stray-quotes') ?> <code>&lt;/p&gt;</code></span> </td></tr>
    
    <tr valign="top"><th scope="row"><?php _e('The loading message','stray-quotes') ?></th>
    <td colspan="2"><input type="text" size="50" name="stray_loading" value="<?php echo (utf8_decode(htmlspecialchars($loading))); ?>" class="regular-text"  id="ajaxinput2" /><span class="setting-description"><?php _e('<br/>The loading message in between quotes.<br/><strong>Sample value:</strong>','stray-quotes') ?> <code>loading...</code></span></td></tr>
        
	</table>
    <br/>
	<div class="submit">
    <input type="hidden" name="do" value="Update" />
    <input type="submit" value="<?php _e('Update all Settings','stray-quotes') ?> &raquo;" />
    </div>
    
    <p>&nbsp;</p>
    
	<?php //new quotes ?>
    <p><h3 style="line-height:.1em"><?php _e('New quotes','stray-quotes') ?></h3>
    <span class="setting-description"><?php _e('Default settings when you create a new quote.','stray-quotes') ?></span>
    </p>
    <table class="form-table">
    
    <tr valign="top"><th scope="row"><?php _e('Visibility','stray-quotes') ?></th>       
        <td colspan="2"><input type="checkbox" name="default_visible" value="Y" <?php echo ($defaultVisible_selected); ?> /><span class="setting-description">
        <?php _e('If checked, will set "Visible" to "Yes" for all new quotes.','stray-quotes') ?></span>
    </td></tr>
    
    <tr valign="top" style="background:#F0F0F0"><th scope="row"><?php _e('Default category','stray-quotes') ?></th>       
    <td colspan="2"><select name="default_category" style="vertical-align:middle"> 
    <?php $categorylist = make_categories(); 
	foreach($categorylist as $categoryo){ ?>
    	<option value="<?php echo $categoryo; ?>" 
		<?php  if ( $categoryo == $defaultcategory) echo ' selected '; ?> >
		<?php echo $categoryo;?></option>
	<?php } ?>   
    </select><span class="setting-description"> 
	<?php _e('This category will be the default for all new quotes.','stray-quotes') ?></span>
	</td></tr>
    
    <tr valign="top"><th scope="row"><?php _e('Clear the form','stray-quotes') ?></th>       
        <td colspan="2"><input type="checkbox" name="clear_form" value="Y" <?php echo ($clearform_selected); ?> /><span class="setting-description">
        <?php _e('If checked, will clear the values from the form after adding a new quote.','stray-quotes') ?></span>
    </td></tr> 
    
	</table>
    <br/>
	<div class="submit">
    <input type="hidden" name="do" value="Update" />
    <input type="submit" value="<?php _e('Update all Settings','stray-quotes') ?> &raquo;" />
    </div>
    
    <p>&nbsp;</p>
     
	<?php //management of quotes ?>    
    <p><h3 style="line-height:.1em"><?php _e('Management of the quotes','stray-quotes') ?></h3>
    <span class="setting-description"><?php _e('Default settings for the management page.  They can be changed on the management page as well.','stray-quotes') ?></span>
    </p>
    <table class="form-table">
     <tr valign="top"><th scope="row"><?php _e('Order by','stray-quotes') ?></th>       
        <td><select name="order" style="vertical-align:middle" >
        <option value="quoteID" <?php if ($order == "quoteID") echo 'selected="selected"'; ?> >ID</option>
        <option value="author" <?php if ($order == "author") echo 'selected="selected"'; ?> >Author</option>
        <option value="source" <?php if ($order == "source") echo 'selected="selected"'; ?> >Source</option>
        <option value="category" <?php if ($order == "category") echo 'selected="selected"'; ?> >Category</option>
        <option value="visible" <?php if ($order == "visible") echo 'selected="selected"'; ?> >Visibility</option>
        </select><span class="setting-description">
        <?php _e('<br/>The list of quotes in the management page will be ordered by this value.','stray-quotes') ?></span>
      </td>
      <td><select name="sort" style="vertical-align:middle" >
        <option value="ASC" <?php if ($sort == "ASC") echo 'selected="selected"'; ?> >Ascending</option>
        <option value="DESC" <?php if ($sort == "DESC") echo 'selected="selected"'; ?> >Descending</option>
        </select><span class="setting-description">
        <?php _e('<br/>The sorting of quotes will take this direction.','stray-quotes') ?></span>
      </td></tr>
     <tr valign="top" style="background:#F0F0F0"><th scope="row"><?php _e('Quotes per page','stray-quotes') ?></th>       
        <td colspan="2"><select name="rows" style="vertical-align:middle">
        <option value="10" <?php if ( $rows == 10) echo ' selected';  ?> >10</option>
        <option value="15" <?php if ( $rows == 15) echo ' selected'; ?> >15</option>
        <option value="20" <?php if ( $rows == 20) echo ' selected'; ?> >20</option>
        <option value="30" <?php if ( $rows == 30) echo ' selected'; ?> >30</option>
        <option value="50" <?php if ( $rows == 50) echo ' selected'; ?> >50</option>
        <option value="100" <?php if ( $rows == 100) echo ' selected'; ?> >100</option>
        </select><span class="setting-description">
        <?php _e('The list of quotes in the management page will display this much quotes per page.','stray-quotes') ?></span>
      </td></tr>
    <tr valign="top"><th scope="row"><?php _e('Show categories','stray-quotes') ?></th>       
    <td colspan="2"><select name="categories" style="vertical-align:middle"> 
    <option value="<?php echo $urlcategory.'all'; ?>" 
	<?php  if ( $categories == '' || $categories == 'all' ) echo ' selected'; ?>><?php _e('All categories','stray-quotes') ?></option>
    <?php $categorylist = make_categories(); 
	foreach($categorylist as $categoryo){ ?>
    	<option value="<?php echo $urlcategory.$categoryo; ?>" 
		<?php  if ( $categories) {if ( $categories == $categoryo) echo ' selected';} ?> >
		<?php echo $categoryo;?></option>
	<?php } ?>   
    </select><span class="setting-description"> 
	<?php _e('The list of quotes in the management page will present quotes from this category only.','stray-quotes') ?></span>
	</td></tr>
	</table>
    <br/>
	<div class="submit">
    <input type="hidden" name="do" value="Update" />
    <input type="submit" value="<?php _e('Update all Settings','stray-quotes') ?> &raquo;" />
    </div>
    <p>&nbsp;</p>
    
    
   <p>&nbsp;</p>
   
	<?php //Multiuser ?>
    <p><h3 style="line-height:.1em"><?php _e('Multiuser settings','stray-quotes') ?></h3>
    <span class="setting-description"><?php _e('Allow contributors to access a restricted version of Stray Random Quotes.','stray-quotes') ?></span>
    </p>
    <table class="form-table"> 
    
    <tr valign="top"><th scope="row"><?php _e('Enable multiuser capability','stray-quotes') ?></th>    
        <td colspan="2"><input type="checkbox" name="stray_multiuser" value="Y" <?php echo ($multiuser_selected); ?> /><span class="setting-description"><?php _e(' If checked a contributor (non-administrator) to the blog will be enabled to: create new quotes, edit and manage his own, and access a limited version of the help page.','stray-quotes') ?></span></td></tr>
      
	</table>
    <br/>
	<div class="submit">
    <input type="hidden" name="do" value="Update" />
    <input type="submit" value="<?php _e('Update all Settings','stray-quotes') ?> &raquo;" />
    </div>
    </div>
    </form><?php
	
}

?>