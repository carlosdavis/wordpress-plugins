<?php
/*
Plugin Name: Advanced Excerpt
Plugin URI: http://sparepencil.com/code/advanced-excerpt/
Description: Several improvements over WP's default excerpt. The size of the excerpt can be limited using character or word count, and HTML markup is not removed.
Version: 0.2.2
Author: Bas van Doren
Author URI: http://sparepencil.com/

Copyright 2007 Bas van Doren

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

if(!class_exists('AdvancedExcerpt')) :
class AdvancedExcerpt
{
	var $name;
	var $text_domain;
	var $mb;
	
	var $skip_next_call;
	
	function AdvancedExcerpt()
	{
		$this->name = strtolower(get_class($this));
		$this->text_domain = $this->name;
		$this->skip_next_call = false;
		$this->charset = get_bloginfo('charset');
		
		// Carefully support multibyte languages
		if(extension_loaded('mbstring') && function_exists('mb_list_encodings'))
			$this->mb = in_array($this->charset, mb_list_encodings());
		
		load_plugin_textdomain($this->text_domain, PLUGINDIR . '/advanced-excerpt/');
		
		register_activation_hook(__FILE__, array(&$this, 'install'));
		
		// Nothing to do on deactivation
		//register_deactivation_hook(__FILE__, array(&$this, 'uninstall'));
		
		add_action('admin_menu', array(&$this, 'add_pages'));
		
		// Replace the default filter (see /wp-includes/default-filters.php)
		remove_filter('get_the_excerpt', 'wp_trim_excerpt');
		add_filter('get_the_excerpt', array(&$this, 'filter'));
	}
	
	function __construct()
	{
		self::AdvancedExcerpt();
	}
	
	function filter($text, $length = null, $use_words = null, $ellipsis = null, $allowed_tags = null, $no_custom = null, $api_used = false)
	{
		global $id, $post;
		
		// Avoid trouble when the API (aka template tag) was used
		if($this->skip_next_call)
		{
			$this->skip_next_call = false;
			return $text;
		}
		$this->skip_next_call = $api_used;
		
		$no_custom = (!is_null($no_custom)) ? (int) (bool) $no_custom : get_option($this->name . '_no_custom');
		
		// Only make the excerpt if it does not exist or 'No Custom Excerpt' is set to true
		if('' == $text || $no_custom)
		{
			$length = (!is_null($length)) ? (int) $length : get_option($this->name . '_length');
			$use_words = (!is_null($use_words)) ? (int) (bool) $use_words : get_option($this->name . '_use_words');
			$ellipsis = (!is_null($ellipsis)) ? $ellipsis : get_option($this->name . '_ellipsis');
			
			$allowed_tags = (is_array($allowed_tags)) ? $allowed_tags : get_option($this->name . '_allowed_tags');
			$allowed_tags = '<' . implode('><', $allowed_tags) . '>';
			
			$text = apply_filters('the_content', get_the_content(''));
			
			// From the default wp_trim_excerpt():
			// Some kind of precaution against malformed CDATA in RSS feeds I suppose
			$text = str_replace(']]>', ']]&gt;', $text);
			$text = strip_tags($text, $allowed_tags);
			
			if(1 == $use_words)
			{
				// Count words, not HTML tags
				if($length > count(preg_split('/[\s]+/', strip_tags($text), -1)))
					return $text;
				
				// Now we start counting
				$text_bits = preg_split('/([\s]+)/', $text, -1, PREG_SPLIT_DELIM_CAPTURE);
				$in_tag = false;
				$n_words = 0;
				$text = '';
				foreach($text_bits as $chunk)
				{
					// Determine whether a tag is opened (and not immediately closed) in this chunk
					if(0 < preg_match('/<[^>]*$/s', $chunk))
						$in_tag = true;
					elseif(0 < preg_match('/>[^<]*$/s', $chunk))
						$in_tag = false;
					
					// This should check if there is a word before the tag
					// but it doesn't work very well
					/*
					if($in_tag && $this->substr($chunk, 0, 1) != '<')
						$n_words++;
					*/
					
					// Is there a word?
					if(!$in_tag && '' != trim($chunk) && $this->substr($chunk, -1, 1) != '>')
						$n_words++;
					
					$text .= $chunk;
					
					if($n_words >= $length && !$in_tag)
						break;
				}
				$text = $text . $ellipsis;
			}
			else
			{
				// Count characters, not whitespace, not those belonging to HTML tags
				if($length > $this->strlen(strip_tags($text)))
					return $text;
				
				$in_tag = false;
				$n_chars = 0;
				for($i = 0; $n_chars < $length || $in_tag; $i++)
				{
					// Is the character worth counting (ie. not part of an HTML tag)
					if($this->substr($text, $i, 1) == '<')
						$in_tag = true;
					elseif($this->substr($text, $i, 1) == '>')
						$in_tag = false;
					elseif(!$in_tag && '' != trim($this->substr($text, $i, 1)))
						$n_chars++;

					// Prevent eternal loops (this could happen with incomplete HTML tags)
					if($i >= $this->strlen($text) - 1)
						break;
				}
				$text = $this->substr($text, 0, $i) . $ellipsis;
			}
			$text = force_balance_tags($text);
		}
		return $text;
	}
	
	function update_options()
	{
		$length = (int) $_POST[$this->name . '_length'];
		$use_words = ('on' == $_POST[$this->name . '_use_words']) ? 1 : 0 ;
		$no_custom = ('on' == $_POST[$this->name . '_no_custom']) ? 1 : 0 ;
		
		$ellipsis = (get_magic_quotes_gpc() == 1) ? stripslashes($_POST[$this->name . '_ellipsis']) : $_POST[$this->name . '_ellipsis'];
		$ellipsis = $ellipsis;
		
		$allowed_tags = (array) $_POST[$this->name . '_allowed_tags'];
		
		update_option($this->name . '_length', $length);
		update_option($this->name . '_use_words', $use_words);
		update_option($this->name . '_no_custom', $no_custom);
		update_option($this->name . '_ellipsis', $ellipsis);
		update_option($this->name . '_allowed_tags', $allowed_tags);
	?>
	<div id="message" class="updated fade"><p>Options saved.</p></div>
	<?php
	}
	
	
	function page_options()
	{
		global $allowedposttags;
		
		if ('POST' == $_SERVER['REQUEST_METHOD'])
		{
			check_admin_referer($this->name . '_update_options');
			$this->update_options();
		}
		
		$length = get_option($this->name . '_length');
		$use_words = get_option($this->name . '_use_words');
		$no_custom = get_option($this->name . '_no_custom');
		$ellipsis = htmlentities(get_option($this->name . '_ellipsis'));
		$allowed_tags = get_option($this->name . '_allowed_tags');
?>
<div class="wrap">
	<div id="icon-options-general" class="icon32"><br /></div>
	<h2><?php _e("Advanced Excerpt Options", $this->text_domain); ?></h2>
	<form method="post" action="">
	<?php
		if ( function_exists('wp_nonce_field') )
			wp_nonce_field($this->name . '_update_options'); ?>
		
		<table class="form-table">
			<tr valign="top">
				<th scope="row"><label for="<?php echo $this->name ?>_length"><?php _e("Excerpt Length:", $this->text_domain); ?></label></th>
				<td>
					<input name="<?php echo $this->name ?>_length" type="text" id="<?php echo $this->name ?>_length" value="<?php echo $length; ?>" size="2" />
					<input name="<?php echo $this->name ?>_use_words" type="checkbox" id="<?php echo $this->name ?>_use_words" value="on" <?php echo (1 == $use_words) ? 'checked="checked" ': ''; ?>/> <?php _e("Use words?", $this->text_domain); ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="<?php echo $this->name ?>_ellipsis"><?php _e("Ellipsis:", $this->text_domain); ?></label></th>
				<td>
					<input name="<?php echo $this->name ?>_ellipsis" type="text" id="<?php echo $this->name ?>_ellipsis" value="<?php echo $ellipsis; ?>" size="5" /> <?php _e('(use <a href="http://www.w3schools.com/tags/ref_entities.asp">HTML entities</a>)', $this->text_domain); ?>
					<br />
					<?php _e("Will substitute the part of the post that is omitted in the excerpt.", $this->text_domain); ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><label for="<?php echo $this->name ?>_no_custom"><?php _e("No Custom Excerpts:", $this->text_domain); ?></label></th>
				<td>
					<input name="<?php echo $this->name ?>_no_custom" type="checkbox" id="<?php echo $this->name ?>_no_custom" value="on" <?php echo (1 == $no_custom) ? 'checked="checked" ': ''; ?>/>
					<?php  _e("Generate excerpts even if a post has a custom excerpt attached.", $this->text_domain); ?>
				</td>
			</tr>
			<tr valign="top">
				<th scope="row"><?php _e("Keep Markup:", $this->text_domain); ?></th>
				<td>
					<table>
<?php
		$i = 0;
		foreach($allowedposttags as $tag => $spec) :
			if(1 == $i / 4) : ?><tr><?php endif;
			$i++;
		?>
					<td><input name="<?php echo $this->name ?>_allowed_tags[]" type="checkbox" id="<?php echo $this->name ?>_allow_<?php echo $tag; ?>" value="<?php echo $tag; ?>" <?php echo (in_array($tag, $allowed_tags)) ? 'checked="checked" ': ''; ?>/> <code><?php echo $tag; ?></code></td><?php if(1 == $i / 4) : $i = 0; ?></tr><?php endif;?>
<?php
		endforeach;
		if(1 != $i / 4) : ?><td colspan="<?php echo (4 - $i); ?>">&nbsp;</td></tr><?php endif;?>
					</table>
					<!--<?php _e("See <a href=\"http://xref.redalt.com/wptrunk/nav.htm?wp-includes/kses.php.source.htm\"><code>wp-includes/kses.php</code></a> if you want to control more tags.", $this->text_domain); ?>-->
				</td>
			</tr>
		</table>
		<p class="submit"><input type="submit" name="Submit" class="button-primary" value="<?php _e("Save Changes", $this->text_domain); ?>" /></p>
	</form>
</div>
	<?php
	}
	
	function add_pages()
	{
		add_options_page(__("Advanced Excerpt Options", $this->text_domain), __("Excerpt", $this->text_domain), 'manage_options', 'options-' . $this->name, array(&$this, 'page_options'));
	}
	
	function install()
	{
		global $allowedposttags;
		foreach($allowedposttags as $tag => $spec)
			$allowed_tags[] = $tag;
		add_option($this->name . '_length', 40);
		add_option($this->name . '_use_words', 1);
		add_option($this->name . '_no_custom', 0);
		add_option($this->name . '_ellipsis', '&hellip;');
		add_option($this->name . '_allowed_tags', $allowed_tags);
	}
	
	function uninstall()
	{
		// Nothing to do (note: deactivation hook is also disabled)
	}
	
	// Careful multibyte support (fallback to normal functions if not available)
	function substr($str, $start, $length = null)
	{
		$length = (is_null($length)) ? $this->strlen($str) : $length;
		if($this->mb)
			return mb_substr($str, $start, $length, $this->charset);
		else
			return substr($str, $start, $length);
	}
	
	function strlen($str)
	{
		if($this->mb)
			return mb_strlen($str, $this->charset);
		else
			return strlen($str);
	}
	
	// PHP seems to lack a simple complement function for arrays
	function array_complement($a, $b)
	{
		$a = array_unique($a);
		$b = array_unique($b);
		$c = array();
		foreach($a as $t)
		{
			if(!in_array($t, $b))
				$c[] = $t;
		}
	}
}

$advancedexcerpt = new AdvancedExcerpt();

function the_advanced_excerpt($args = '')
{
	global $advancedexcerpt;
	$defaults = array(
	'length' => get_option($advancedexcerpt->name . '_length'),
	'use_words' => get_option($advancedexcerpt->name . '_use_words'),
	'no_custom' => get_option($advancedexcerpt->name . '_no_custom'),
	// URL encode, because URL decode is used on this setting later
	'ellipsis' => urlencode(get_option($advancedexcerpt->name . '_ellipsis')),
	'allow_tags' => implode(',', get_option($advancedexcerpt->name . '_allowed_tags')),
	'exclude_tags' => '');
	
	$r = wp_parse_args($args, $defaults);
	
	extract($r, EXTR_SKIP);

	$allow_tags = preg_split('/[\s,]+/', $allow_tags);
	$exclude_tags = preg_split('/[\s,]+/', $exclude_tags);
	$ellipsis = urldecode($ellipsis);
	
	// {allow_tags} - {exclude_tags}
	$allow_tags = $advancedexcerpt->array_complement($allow_tags, $exclude_tags);
	
	// All filters are applied, however, the advanced excerpt now behaves like a priorite 1 filter, instead of the default priority 10
	$text = $advancedexcerpt->filter('', $length, $use_words, $ellipsis, $allow_tags, $no_custom, true);
	$text = apply_filters('get_the_excerpt', $text);
	echo apply_filters('the_excerpt', $text);
}
endif;