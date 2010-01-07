<?php

//MAIN FUNCTION. this RETURNS one or more random quotes.
function get_stray_quotes($categories=NULL,$sequence=NULL,$linkphrase=NULL,$multi=NULL,$timer=NULL,$noajax=NULL,$myoffset=0,$widgetid=NULL,$fullpage=NULL,$orderby='quoteID',$sort='ASC',$thisid=NULL,$disableaspect=NULL,$contributor=NULL) {

	global $wpdb;

	//the variables
	$quotesoptions = array();
	$quotesoptions = get_option('stray_quotes_options');
	$beforeAll =  utf8_decode($quotesoptions['stray_quotes_before_all']);
	$afterAll = utf8_decode($quotesoptions['stray_quotes_after_all']);
	$beforeloader = utf8_decode($quotesoptions['stray_before_loader']);
	$quoteloader = utf8_decode($quotesoptions['stray_loader']);
	$afterloader = utf8_decode($quotesoptions['stray_after_loader']);
	$strayajax = $quotesoptions['stray_ajax'];
	$loading =  utf8_decode($quotesoptions['stray_loading']);

	//handle the categories
	if ( $categories && ($categories !='' && $categories !='all') ) {
	
		if (is_string($categories))$categories = explode(",", $categories);
		
		if (count($categories) == 1) {
			$categoryquery = ' AND `category`=\''. $categories[0] .'\'';
		} else { 
			$categoryquery = ' AND `category`=\'';
		
			foreach ($categories as $category) {
				$category = trim($category);
				$categoryquery .= $category.'\' OR `category`=\'';
			}
			$categoryquery = substr($categoryquery,0,-17);
			$categoryquery .='\'';
		}
	} else {
		$categoryquery = '';
		$categories = '';
	}
	
	//generate a casual id if the function is not called via a widget
	if (is_string($widgetid)) settype($widgetid, "integer"); 
	if (!$widgetid)$widgetid = mt_rand(0,999999);
	
	//make sure certain values are not string or empty
	if (is_string($sequence)) {
		if ($sequence == 'false' || $sequence == '') {
			settype($sequence, "boolean");
			$sequence = false;			
		} else if ($sequence == 'true') {
			settype($sequence, "boolean");
			$sequence = true;			
		} 
	}
	if(is_string($multi) || is_bool($multi)) settype($multi, "integer");
	if($multi == 0 || $multi == '' || false == $multi)$multi = 1;
	if(is_string($timer)) settype($timer, "integer");
	if($timer == '' || false === $timer)$timer = 0;
	if (is_string($noajax)) {
		if ($noajax == 'false') {
			settype($noajax, "boolean");
			$noajax = false;			
		} else if ($noajax == 'true') {
			settype($noajax, "boolean");
			$noajax = true;			
		} else {
			settype($noajax, "boolean");
			$noajax = false;			
		}
	}	
	if (is_string($fullpage)) {
		if ($fullpage == 'false') {
			settype($fullpage, "boolean");
			$fullpage = false;			
		} else if ($fullpage == 'true') {
			settype($fullpage, "boolean");
			$fullpage = true;			
		} else {
			settype($fullpage, "boolean");
			$fullpage = false;			
		}
	}	
	if($orderby=='')$orderby='quoteID';
	if($sort=='')$sort='ASC';
	if (is_string($disableaspect)) {
		if ($disableaspect == 'false' || $disableaspect == '' || $disableaspect == '0' ) {
			settype($disableaspect, "boolean");
			$disableaspect = false;			
		} else if ($disableaspect == 'true' || $disableaspect == '1' ) {
			settype($disableaspect, "boolean");
			$disableaspect = true;			
		} else {
			settype($disableaspect, "boolean");
			$disableaspect = false;			
		}
	}
	
	//handle contributors
	$multiuser = $quotesoptions['stray_multiuser'];
	if ( $contributor && $multiuser == 'Y' ) {
		
		if ($categoryquery != '')
			$userquery = ' AND `user`=\''. $contributor.'\'';
		else
			$userquery = ' WHERE `user`=\''. $contributor.'\'';
	
	}

	//sql for more than one quote
	if ($multi > 1){
		
		// how many rows we have in database?
		$numrows = $wpdb->get_var("SELECT COUNT(`quoteID`) as 'rows' FROM " . WP_STRAY_QUOTES_TABLE . " WHERE visible='yes'" . $categoryquery . $userquery);
		
		// workaround for the "division by zero" problem
		$rows = $multi;
		if (is_string($rows))$rows=intval($rows);
		settype($rows, "integer"); 

		if ($fullpage){
		
			//what page number?
			$pages = 1;
			if(isset($_GET['qp']))$pages = $_GET['qp'];
			if ($strayajax != 'Y' && $noajax != true)$offset=$myoffset;
			else $offset = ($pages - 1) * $rows;		
			
			// how many pages we have when using paging?
			if ($rows == NULL) $rows = 2; 
			$maxPage = ceil($numrows/intval($rows));
			
			// print the link to access each page
			$nav  = '';		
			$baseurl = remove_querystring_var($_SERVER['REQUEST_URI'], 'qp');
			if (strpos( $baseurl,'?'))$urlpages = $baseurl.'&qp=';
			else $urlpages = $baseurl.'?qp=';
			
			for($quotepage = 1; $quotepage <= $maxPage; $quotepage++) {
			   if ($quotepage == $pages)$nav .= $quotepage; // no need to create a link to current page
			   else $nav .= ' <a href="'.$urlpages.$quotepage.'">'.$quotepage.'</a> ';
			}
			
			if ($pages > 1) {
			   $quotepage  = $pages - 1;
			   $prev  = ' <a href="'.$urlpages.$quotepage.'">Previous '.$rows.'</a> | ';		
			   $first = ' <a href="'.$urlpages.'1">First</a> | ';
			}
			else {
			   $prev  = '&nbsp;'; // we're on page one, don't print previous link
			   $first = '&nbsp;'; // nor the first page link
			}
			
			if ($pages < $maxPage) {
			
				$missing = $numrows-($rows*$pages);		
				if ($missing > $rows) $missing = $rows;
				
				$quotepage = $pages + 1;
				$next = ' | <a href="'.$urlpages.$quotepage.'"> Next '.$missing.'</a> ';
				
				$last = ' | <a href="'.$urlpages.$maxPage.'"> Last</a> ';
			}
			else {
			   $next = '&nbsp;'; // we're on the last page, don't print next link
			   $last = '&nbsp;'; // nor the last page link
			}		
			
			$loader = $first . $prev . $nav . $next . $last;
			
		} else {
			
			//what page number?
			$pages = 1;
			if(isset($_GET['qmp']))$pages = $_GET['qmp'];
			if ($strayajax != 'Y' && $noajax != true)$offset=$myoffset;
			else $offset = ($pages - 1) * $rows;
			
			// how many pages we have when using paging?
			if ($rows == NULL) $rows = 2; 
			$maxPage = ceil($numrows/intval($rows));
			
			$baseurl = remove_querystring_var($_SERVER['REQUEST_URI'], 'qmp');
			if (strpos( $baseurl,'?'))$urlpages = $baseurl.'&qmp=';
			else $urlpages = $baseurl.'?qmp=';		
			
			if ($pages > 1) {
			   $quotepage  = $pages - 1;
			   $prev  = '<a href="'.$urlpages.$quotepage.'">&laquo; Previous '.$rows.'</a>&nbsp;|';		
			}
			else {
			   $prev  = '&nbsp;'; // we're on page one, don't print previous link
			}
			
			if ($pages < $maxPage) {
			
				$missing = $numrows-($rows*$pages);		
				if ($missing > $rows) $missing = $rows;
				
				$quotepage = $pages + 1;
				$next = '&nbsp;<a href="'.$urlpages.$quotepage.'">Next '.$missing.' &raquo;</a>';
				
			}
			else {
			   $next = '&nbsp;'; // we're on the last page, don't print next link
			}
			
			$loader = $prev.$next;
			
		}
		
		//if random
		if (!$sequence || $sequence === false) { 
			$orderby ='RAND()';
			$sort = '';
		} else {
			$orderby ="`".$orderby."` ";
		}
		
		//retrieve the quotes
		$sql = "SELECT `quoteID`,`quote`,`author`,`source` FROM `" 
		. WP_STRAY_QUOTES_TABLE . "` WHERE `visible`='yes'" . $categoryquery . $userquery
		. " ORDER BY ". $orderby . $sort 
		. " LIMIT " . $offset. ", ". $multi;
		$offset = $myoffset+$multi;
		
		$result = $wpdb->get_results($sql);
		$totalquotes = count($result)-1;
	
	} 
	
	//sql for one quote only
	else {		
		
		//sql the quotes
		$offset=0;		
		$sql = "SELECT `quoteID`,`quote`,`author`,`source` FROM `" 
		. WP_STRAY_QUOTES_TABLE . "` WHERE `visible`='yes'" .$categoryquery . $userquery
		. " ORDER BY ". "`".$orderby."` " . $sort ;

		$result = $wpdb->get_results($sql);
		$totalquotes = count($result)-1;
			
		//if it is a specific quote
		if ( $thisid && $thisid != '' && !is_int($sequence) ){
			
			foreach($result as $get_one){
				
				if ($get_one->quoteID == $thisid) {
					
					$specificresult = $get_one;
					settype($sequence,"integer");
					for ($i=0; $i<count($result); ++$i) {
						if ($get_one==$result[$i]) $sequence = $i;
					}
					/*$sequence = array_search($get_one,$result);
					settype($sequence,"integer");*/
					$sequence = $sequence-1;
				
				}
			}
			
		}
				
	}
	
	//if the sql has something to say, build the output
	if ( !empty($result) ){	
	
		$output = '';
			
		//if ajax loader is NOT disabled
		if ($strayajax != 'Y' && $noajax != true ) {
			
			//make sure to reset offset when there are no more quotes
			if ($multi > 1)if($offset >= $numrows) $offset = 0;
			
			//make things into a string (for javascript)
			if(is_array($categories))$categories = implode(',', $categories);

			//check and make the random/not random thing
			if ($sequence) {
				
				//if $sequence=true bool, make it a random number
				if ($sequence === true){
					 settype($sequence, "integer"); 	
					 $sequence = mt_rand(0, $totalquotes);
				} 
				
				//or forward the sequence
				else {

					//if it is not already a number		
					settype($sequence, "integer"); 
								
					//start over when the last one is reached
					if ($sequence == $totalquotes)$sequence = 0;
					//grow the sequence
					else $sequence = $sequence+1;
					
					//this otherwise "0" would be considered as "false" by javascript
					if ($sequence == 0) {
						settype($sequence,"string"); 
						$sequence == '0';
					}
					
				}
			}
		
			//override default new quote loader
			if ($linkphrase)$quoteloader = $linkphrase;
					
			//the javascript event with all the variables
			$jaction = 'newQuote(\''.
			$categories .'\',\''.
			urlencode($quoteloader).'\',\''.
			$widgetid.'\',\''.
			WP_STRAY_QUOTES_PATH.'\',\''.
			$multi.'\',\''.
			$offset.'\',\''.
			$sequence.'\',\''.
			$timer.'\',\''.
			$disableaspect.'\',\''.
			$loading.'\',\''.
			$contributor.'\')';
			
			$event = 'onclick="'.$jaction.'"';
			
			//click on the quote itself or on the link (part 1)
			if (!$quoteloader && $timer < 2) $output .= '<div class="stray_quote-'.$widgetid.'" '.$event.' >';
			else  $output .= '<div class="stray_quote-'.$widgetid.'">';
		}
		
		//output if multi quote
		if ($multi > 1){
			
			$output .= $beforeAll.'<ul>';
			foreach ( $result as $get_one )
				$output .= '<li>'.stray_output_one($get_one,$multi,$disableaspect).'</li>';	
			$output .= '</ul>'.$afterAll;
			
		} 
		
		//output if one quote only
		else {
			
			//if a specific quote
			if ( $thisid && $thisid != '' )$output .= stray_output_one($specificresult,$multi,$disableaspect);
			
			//if it is not a specific quote
			else {
				//get the next quote in sequence
				if ($sequence)$get_one = $result[$sequence];
				
				//get the quote randomly
				else $get_one = $result[mt_rand(0, $totalquotes)];
				$output .= stray_output_one($get_one,$multi,$disableaspect);
			}
			
		}
		
		//if ajax loader is NOT disabled
		if ($strayajax != 'Y' && $noajax != true ) {
				
			//if you click on the link (part 2)
			if ($quoteloader && $timer < 2) {
				
				$output .= $beforeloader;
				$output .= '<a '.$event.' style="cursor:pointer" >'. $quoteloader.'</a>';
				$output .= $afterloader;
			}
			
			$output .= '</div>';	
			
			//timeout thing
			if ($timer)$output .= '<script type="text/javascript">setTimeout("'.$jaction.'", "'.$timer.'000");</script>';
			
		} 
		
		//AJAX disabled AND many quotes (output the pagination)
		else if ($multi > 1) {
			
			$output .= $beforeloader;
			$output .= $loader;
			$output .= $afterloader;			
		}
		
		return $output;
	}
}

//this is a TEMPLATE TAG. It ECHOES one or more random quotes.
function stray_random_quote($categories='all',$sequence=false,$linkphrase='',$noajax=false,$multi=1,$timer=0,$orderby='quoteID',$sort='ASC',$disableaspect=NULL, $contributor=NULL) {
	echo get_stray_quotes($categories,$sequence,$linkphrase,$multi,$timer,$noajax,0,'',false,$orderby,$sort,'',$disableaspect, $contributor);	
}

//this is a TEMPLATE TAG. It ECHOES a specific quote.
function stray_a_quote($id=1,$linkphrase='',$noajax=false,$disableaspect=NULL) {
	echo get_stray_quotes('',true,$linkphrase,'','',$noajax,'','','','','',$id,$disableaspect);
}

//this is a SHORTCODE [stray-random]
function stray_random_shortcode($atts, $content=NULL) {
	
	extract(shortcode_atts(array(
	"categories" => 'all',
	"sequence" => false,
	"linkphrase" => '',
	"widgetid" => '', 
	"noajax" => '',
	"multi" => 1,
	"timer" => '',
	"offset" => 0,
	"fullpage" => '',
	"disableaspect" => false,
	"user" => ''
	), $atts));	
	
	return get_stray_quotes($categories,$sequence,$linkphrase,$multi,$timer,$noajax,$offset,$widgetid,$fullpage,'quoteID','ASC','',$disableaspect, $user);
}

//this is a SHORTCODE [stray-all]
function stray_all_shortcode($atts, $content=NULL) {

	extract(shortcode_atts(array(
	"categories" => 'all',
	"sequence" => true,
	"linkphrase" => '',
	"widgetid" => '', 
	"noajax" => true,
	"rows" => 10,
	"timer" => '',
	"offset" => 0,
	"fullpage" => true,
	"orderby" =>'quoteID',
	"sort" => 'ASC',
	"disableaspect" => false,
	"user" => ''
	), $atts));	
	
	return get_stray_quotes($categories,$sequence,$linkphrase,$rows,$timer,$noajax,$offset,$widgetid,$fullpage,$orderby,$sort,'',$disableaspect, $user);
}

//this is a SHORTCODE [stray-id]
function stray_id_shortcode($atts, $content=NULL) {

	extract(shortcode_atts(array(
	"id" => '1',
	"linkphrase" => '',
	"noajax" => true,
	"disableaspect" => false
	), $atts));	
	
	return get_stray_quotes('',true,$linkphrase,'','',$noajax,'','','','','',$id,$disableaspect);
}

//this FORMATS a given quote according to the settings
function stray_output_one($get_one,$multi=NULL,$disableaspect=NULL) {

	//the variables
	$quotesoptions = array();
	$quotesoptions = get_option('stray_quotes_options');
	if(!$disableaspect || $disableaspect == '' || $disableaspect == false){
		if($multi == 1 || $multi == '' || $multi==false){
			$beforeAll =  utf8_decode($quotesoptions['stray_quotes_before_all']);
			$afterAll = utf8_decode($quotesoptions['stray_quotes_after_all']);
		}
		$beforeQuote = utf8_decode($quotesoptions['stray_quotes_before_quote']);
		$afterQuote = utf8_decode($quotesoptions['stray_quotes_after_quote']);
		$beforeAuthor = utf8_decode($quotesoptions['stray_quotes_before_author']);
		$afterAuthor = utf8_decode($quotesoptions['stray_quotes_after_author']);
		$beforeSource = utf8_decode($quotesoptions['stray_quotes_before_source']);
		$afterSource = utf8_decode($quotesoptions['stray_quotes_after_source']);
		$linkto = utf8_decode($quotesoptions['stray_quotes_linkto']);
		$sourcelinkto = utf8_decode($quotesoptions['stray_quotes_sourcelinkto']);
		$sourcespaces = utf8_decode($quotesoptions['stray_quotes_sourcespaces']);	
		$authorspaces = utf8_decode($quotesoptions['stray_quotes_authorspaces']);
		$ifnoauthor = utf8_decode($quotesoptions['stray_if_no_author']);	
	} else {
		$beforeAuthor = ' ';
		$ifnoauthor = ' ';
		$beforeSource = ' ';	
	}
	
	$putQuotesFirst = utf8_decode($quotesoptions['stray_quotes_put_quotes_first']);
	$output = '';	
		
	//make or not the author link
	if ( $get_one->author ) {
		if ( !$linkto || preg_match("/^[a-zA-Z]+[:\/\/]+[A-Za-z0-9\-_]+\\.+[A-Za-z0-9\.\/%&=\?\-_]+$/i",$get_one->author) )
			$Author = $get_one->author;
		else {
			$Author = $get_one->author;
			if ($authorspaces)$Author =str_replace(" ",$authorspaces,$Author);
			
			$search = array('"', '&', '%AUTHOR%');
			$replace = array('%22','&amp;', $Author);
			$linkto = str_replace($search,$replace,$linkto);
			
			/*$linkto = str_replace('%AUTHOR%',$Author,$linkto);*/
			$Author = '<a href="'.$linkto.'">' . $get_one->author . '</a>';
		}
	}
	
	//make or not the source link
	if ( $get_one->source ) {
		if ( !$sourcelinkto || preg_match("/^[a-zA-Z]+[:\/\/]+[A-Za-z0-9\-_]+\\.+[A-Za-z0-9\.\/%&=\?\-_]+$/i",$get_one->source) )
			$Source = $get_one->source;
		else {
			$Source = $get_one->source;
			if ($sourcespaces)$Source =str_replace(" ",$sourcespaces,$Source);
			
			$search = array('"', '&', '%SOURCE%');
			$replace = array('%22','&amp;', $Source);
			$sourcelinkto = str_replace($search,$replace,$sourcelinkto);
			
			/*$sourcelinkto = str_replace('%SOURCE%',$Source,$sourcelinkto);*/
			$Source = '<a href="'.$sourcelinkto.'">' . $get_one->source . '</a>';
		}
	}
	
	//author first
	if ( !$putQuotesFirst) {
		$output .= $beforeAll;
		
		//if author
		if ( !empty($get_one->author) ) {
			$output .= $beforeAuthor . $Author . $afterAuthor;
			//source values if there is an author
			if ( !empty($get_one->source) ) {
				$output .= $beforeSource . $Source . $afterSource;
			}				
		//source values if there is no author	
		} else {
			if ( !empty($get_one->source) ) {
				$output .= $ifnoauthor . $Source . $afterSource;
			}				
		}

		$output .= $beforeQuote . nl2br($get_one->quote) . $afterQuote;			
		$output .= $afterAll;		
	}
	
	//quote first
	else {	
		
		$output .= $beforeAll;		
		$output .= $beforeQuote . nl2br($get_one->quote) . $afterQuote;
		//if author
		if ( !empty($get_one->author) ) {
			$output .= $beforeAuthor . $Author . $afterAuthor;
			//source values if there is an author
			if ( !empty($get_one->source) ) {
				$output .= $beforeSource . $Source . $afterSource;
			}				
		//source values if there is no author	
		} else {
			if ( !empty($get_one->source) ) {
				$output .= $ifnoauthor . $Source . $afterSource;
			}				
		}
		$output .= $afterAll;		
	}		
	
	//end of story
	return $output;

}

//this creates a LIST of unique CATEGORIES
function make_categories($user=NULL) {
	global $wpdb;
	
	//get default category
	$quotesoptions = array();
	$quotesoptions = get_option('stray_quotes_options');
	$defaultcategory = $quotesoptions['stray_default_category'];
	
	if($user)$where = "` WHERE `user`='".$user."'";
	$allcategories = $wpdb->get_col("SELECT `category` FROM `" . WP_STRAY_QUOTES_TABLE . $where );
	if ($allcategories == false)$allcategories = array($defaultcategory);
	$uniquecategories = array_unique($allcategories);
	return $uniquecategories;
}

//this finds the MOST USED VALUE in a column
function mostused($field) {

	global $wpdb,$current_user;

	$sql = "SELECT `".$field."` FROM `" . WP_STRAY_QUOTES_TABLE . "` WHERE `".$field."` IS NOT NULL AND `".$field."` !='' AND `user`='".$current_user->user_nicename."'" ;
	$all = $wpdb->get_col($sql);
	$array = array_count_values($all);
	
	reset($array);
	if(FALSE === key($array)) {
		return array('min' => NULL, 'max' => NULL);
	}
	
	$min = $max = current($array);
	$val = next($array);
	$atleasttwo = false;
	
	while(NULL !== key($array)) {
		if($val > $max)$max = $val;
		elseif($val < $min)$min = $val;
		if ($val > 1) $atleasttwo = true;
		$val = next($array);
		
	}
	if ($atleasttwo == true) {
		$keys = array_keys($array, $max);
		$maxvalue = $keys[0];
		return $maxvalue;	
	} else return false;
}

//this adds or REPLACES A VARIABLE into a querystring. 
//Thanks to http://www.addedbytes.com/php/querystring-functions/
function querystrings($url, $key, $value) {
	$url = preg_replace('/(.*)(\?|&)' . $key . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&');
	$url = substr($url, 0, -1);
	if (strpos($url, '?') === false) {
		return ($url . '?' . $key . '=' . $value);
	} else {
		return ($url . '&' . $key . '=' . $value);
	}
}

//this REMOVES A VARIABLE from a querystring. 
//Thanks to http://www.addedbytes.com/php/querystring-functions/
function remove_querystring_var($url, $key) {
	$url = preg_replace('/(.*)(\?|&)' . $key . '=[^&]+?(&)(.*)/i', '$1$2$4', $url . '&');
	$url = substr($url, 0, -1);
	return ($url);
}

//this is for compatibility with 2.3 function names
function wp_quotes_random() {return stray_random_quote();}
function wp_quotes($id) {return stray_a_quote($id);}
function wp_quotes_page($data) {return stray_all_shortcode();}

?>