=== AddToAny: Share/Bookmark/Email Button ===
Contributors: micropat
Tags: sharing, share, sharethis, bookmarking, social, social bookmarking, social bookmarks, bookmark, bookmarks, save, Post, posts, page, pages, images, image, admin, statistics, stats, links, plugin, widget, email, e-mail, seo, button, delicious, google buzz, buzz, google, digg, reddit, facebook, myspace, twitter, stumbleupon, technorati, icon, icons, wpmu, addtoany, add, any
Requires at least: 2.8
Tested up to: 3.0
Stable tag: 0.9.9.6.4

Help people share, bookmark, and email your posts & pages using any service, such as Facebook, Twitter, Google Buzz, Digg and many more.

== Description ==

The WordPress plugin to help people **share**, **bookmark**, and **email** your posts and pages using **any service**, such as Facebook, Twitter, Google Buzz, Digg, Delicious, and well over 100 more social bookmarking and sharing sites. The plugin comes with AddToAny's customizable **Smart Menu**, which **places the services visitors use at the top of the menu**, based on each visitor's browsing and usage history.

<a href="http://www.addtoany.com/" title="Sharing and bookmarking button widget" target="_blank">Share Button</a> (demo)

The Email tab makes it easy to share via Google Gmail, Yahoo! Mail, Hotmail, AOL, and any other web-based emailer or desktop program. The **Add to Favorites** button (or Bookmark tab) helps users bookmark using any browser (Internet Explorer, Firefox, Chrome, Safari, Opera, etc.).

Individual **service icons** let you optimize your blog posts for specific social sites.  Choose from over 100 individual services.

* AddToAny <a href="http://www.addtoany.com/blog/smart-menus-the-services-your-visitors-use-displayed-first/" target="_blank">Smart Menu</a>
* Individual service links (like Sociable)
* Includes all <a href="http://www.addtoany.com/services/" target="_blank">services</a>
* Menu updated automatically
* Google Analytics integration (<a href="http://www.addtoany.com/ext/google_analytics/">access guide</a>)
* WordPress optimized, localized (English, Chinese, Spanish, Japanese, French, Portuguese, Italian, Danish, Catalan, Russian, Albanian, Romanian, Belarusian)
* Many more publisher and user features!

After **years of ongoing development**, over **1 million downloads**, and **ongoing support** from the community, AddToAny still strives to be the best WordPress plugin for sharing. We hope it's perfect for everyone by version 1.0.

<a href="http://www.addtoany.com/share_save" title="Share" target="_blank">Share this plugin</a>

See also:

* The <a href="/extend/plugins/add-to-any-subscribe/" title="WordPress RSS Subscribe widget plugin">Subscribe button</a> plugin
* The <a href="http://www.addtoany.com/buttons/for/wordpress_com" title="WordPress.com sharing button widget" target="_blank">Share button for WordPress.com</a> blogs
* The standard <a href="http://www.addtoany.com/buttons/">Share button</a> widget

<a href="http://www.addtoany.com/blog/" target="_blank">AddToAny Blog</a> | <a href="http://www.addtoany.com/privacy">Privacy Policy</a>

== Installation ==

1. Upload the `add-to-any` directory (including all files within) to the `/wp-content/plugins/` directory
1. Activate the plugin through the `Plugins` menu in WordPress

== Frequently Asked Questions ==

= Where are the options? =

In your Admin panel, go to `Settings` > `Share/Save Buttons`.  Among other options, you can choose which button and individual icons to display, and where and how to display them. 

= Why isn't the drop-down menu appearing? =

It's likely because your theme wasn't <a href="http://codex.wordpress.org/Theme_Development#Plugin_API_Hooks" target="_blank">coded properly</a>.  Using the Theme Editor, make sure that the following piece of code is included in your theme's `footer.php` file just before the `</body>` line:

`<?php wp_footer(); ?>`

= How can I add both the button and the individual icons to another area of my theme? =

In the Theme Editor, place this code block where you want the button and individual icons to appear in your theme:

`<?php if( function_exists('ADDTOANY_SHARE_SAVE_KIT') ) { ADDTOANY_SHARE_SAVE_KIT(); } ?>`

If you want to customize the shared URL and title for the button and standalone services, use the following code as a template:

`<?php if( function_exists('ADDTOANY_SHARE_SAVE_KIT') ) { 
	ADDTOANY_SHARE_SAVE_KIT( array("linkname" => "Example Page", "linkurl" => "http://example.com/page.html") );
} ?>`

= How can I add just the button to another area of my theme? =

In the Theme Editor, you will place this line of code where you want the button to appear in your theme:

`<?php if( function_exists('ADDTOANY_SHARE_SAVE_BUTTON') ) { ADDTOANY_SHARE_SAVE_BUTTON(); } ?>`

If you want to customize the shared URL and title for this button, use the following code as a template:

`<?php if( function_exists('ADDTOANY_SHARE_SAVE_BUTTON') ) { 
	ADDTOANY_SHARE_SAVE_BUTTON( array("linkname" => "Example Page", "linkurl" => "http://example.com/page.html") );
} ?>`

= How can I add just the individual icons to another area of my theme? =

In the Theme Editor, place this line of code where you want the individual icons to appear in your theme (within an HTML list):

`<?php echo '<ul class="addtoany_list">';
if( function_exists('ADDTOANY_SHARE_SAVE_ICONS') )
	ADDTOANY_SHARE_SAVE_ICONS( array("html_wrap_open" => "<li>", "html_wrap_close" => "</li>") );
echo '</ul>'; ?>`

If you want to customize the shared URL and title for these icons, use the following code as a template:

`<?php
if( function_exists('ADDTOANY_SHARE_SAVE_ICONS') ) {
	echo '<ul class="addtoany_list">';
	ADDTOANY_SHARE_SAVE_ICONS( array(
		"html_wrap_open" => "<li>", "html_wrap_close" => "</li>",
		"linkname" => "Example Page", "linkurl" => "http://example.com/page.html"
	));
	echo '</ul>';
} ?>`

Or you can place the icons as individual links (without being wrapped in an HTML list):

`<?php if( function_exists('ADDTOANY_SHARE_SAVE_ICONS') ) { ADDTOANY_SHARE_SAVE_ICONS(); } ?>`

If you want to customize the shared URL and title for these icons, use the following code as a template:

`<?php if( function_exists('ADDTOANY_SHARE_SAVE_ICONS') ) {
	ADDTOANY_SHARE_SAVE_ICONS( array("linkname" => "Example Page", "linkurl" => "http://example.com/page.html") );
} ?>`

= How can I add a new custom standalone service? =
You can create a plugin or customize the following PHP sample code to add to your theme's function.php file:

`function addtoany_add_services( $services ) {
  $services['google_example'] = array(
    'name' => 'Google Example',
    'icon_url' => 'http://www.google.com/favicon.ico',
	'icon_width' => 16,
	'icon_height' => 16,
    'href' => 'http://www.example.com/add?linkurl=A2A_LINKURL&amp;linkname=A2A_LINKNAME'
  );
  return $services;
}
add_filter('A2A_SHARE_SAVE_services', 'addtoany_add_services', 10, 1);`

= How can I align the button and/or icons to the center or to the right side of posts? =
It depends on your theme, but you can try adding the following CSS code to your main stylesheet.

To align right:
`.addtoany_share_save_container { text-align:right; }`

To align center:
`.addtoany_share_save_container { text-align:center; }`

= How can I remove a button from individual posts and pages? =

Insert the following tag into the page or post (HTML tab) that you do not want the button to appear in: `<!--nosharesave-->`

= How can I force the button to appear in individual posts and pages? =

If your button isn't already set up to appear (it is by default), insert the following tag into the page or post (HTML tab) that you want the button to appear in: `<!--sharesave-->`

= Why doesn't Facebook use the page title and how can I set the image Facebook uses? =

Facebook does link sharing a little differently than most other services. Facebook will sometimes use the Meta Description of a shared page instead of the page's title or the title you have set for AddToAny.

To change the title, description and/or image on Facebook, you will need to modify your theme header file according to <a href="http://wiki.developers.facebook.com/index.php/Facebook_Share/Specifying_Meta_Tags">Facebook's specification</a>. With WordPress, this can be accomplished with plugins like the <a href="http://wordpress.org/extend/plugins/all-in-one-seo-pack/">All in One SEO Pack plugin</a>.  Please see that plugin for details, and post in the WordPress or plugin author's forums for more support.

= Does the plugin output W3C valid code? =

Yes, AddToAny outputs 100% W3C valid XHTML and W3C valid CSS 3.0.

If you move the plugin's CSS code to your theme's stylesheet, note that one proprietary vendor prefix (`filter`) is used.  It's utilized for cross-browser compatibility with Internet Explorer.  For more information about the W3C CSS Validator and the `filter` property, see <a href="http://www.websitedev.de/css/validator-faq#extensions">Why don't my scrollbar properties, filters, etc. validate?</a>  If you feel compelled, you can move the line into an Internet Explorer-only stylesheet, or remove the line at the cost of the opacity hover effect in Internet Explorer.

= How can I move the plugin's inline CSS into my theme's main external stylesheet? =

Go to `Settings` > `Share/Save Buttons` > uncheck `Use inline CSS` and place the CSS code in your theme's main stylesheet.

= Why does the menu appear behind embedded objects (like Flash)? =

Please read <a href="http://www.addtoany.com/buttons/customize/show_over_embeds">this document</a> for an explanation and possible fix. For WordPress, an easier fix is to have AddToAny <a href="http://www.addtoany.com/buttons/customize/hide_embeds">hide intersecting embedded objects</a>. Just copy & paste the recommended code into the Additional Options box in `Settings` > `Share/Save Buttons`, then save changes.

== Screenshots ==

1. AddToAny Share/Save button, featuring the Open Share Icon
2. Drop-down menu that appears instantly when visitors use the share button
3. Email tab, with direct links to the most popular web-based emailers' auto-filled Compose page, a web-based sender for use with any email address, and a link for desktop email clients
4. Settings panel
5. Color chooser for your AddToAny menus

== Changelog ==

= .9.9.6.4 =
* Option to toggle displaying at the bottom of excerpts
* Shorten option descriptions
* Deprecate button link target option
* Rearrange menu options by popularity

= .9.9.6.3 =
* Any Email now a standalone service
* Other new standalone services
 * Diary.Ru
 * Tuenti
 * VK (VKontakte.ru)
 * XING
* Remove defunct services

= .9.9.6.2 =
* Remove stylesheet on other admin pages
* Fix RTL support in admin
* Fix minor bug where Add/Remove Services button reappears after choosing a standalone service in admin 
* Remove jQuery UI Sortable on other admin pages
* Portuguese translation updated (by <a href="http://jorgesilva.me/">Jorge</a>

= .9.9.6.1 =
* Fix critical bug introduced in last release that caused an empty shared item to be used on non-single post pages 
 * Thanks to <a href="http://www.macmanx.com/">James</a> for the report!

= .9.9.6 =
* Require WordPress 2.8
 * For WordPress versions 2.0 through 2.7.1 use version .9.9.5.9 (<a href="http://downloads.wordpress.org/plugin/add-to-any.0.9.9.5.9.zip">download .zip</a>) or <a href="http://wordpress.org/extend/plugins/add-to-any/download/">earlier</a>
* Fix validation problem caused by WordPress' lack of support for CDATA sections in posts
 * http://core.trac.wordpress.org/ticket/3670 - WordPress fix slated for 3.1, then @todo reevaluate 
* Replace deprecated functions
* Internal security improvements (no known issues, just precautionary stuff for rare circumstances)
* Remove legacy parameters on HREF to accomodate efficient hash params via menu JS
* Remove extraneous semicolons within STYLE attributes for icon+text button
* Variable cleanup in _BUTTON and _ICONS functions for legibility

= .9.9.5.9 =
* Nonce validation for admin form
* Fixes for debug mode notices
* Russian translation updated (by Elvis)

= .9.9.5.8 =
* Wrap inline JavaScript in CDATA section for better XHTML compliance
* Deprecate embedded object hiding option in favor of Menu API due to a new automatic workaround and a change in default value
 * If you need to have AddToAny hide objects (like Flash) to display the AddToAny menu, set a2a_config.hide_embeds = 1; in the Additional Options box

= .9.9.5.7 =
* Work around "Featured Posts" excerpt scenarios that would stop button auto-placement below post content  
* Work around WP core issue to use HTTPS/SSL for static content files, like standalone icons and files in admin
 * http://core.trac.wordpress.org/ticket/13941
* Admin style consistency

= .9.9.5.6 =
* Fix PHP warnings when there are no arguments from functions ADDTOANY_SHARE_SAVE_BUTTON or ADDTOANY_SHARE_SAVE_ICONS
* Italian translation update (by <a href="http://gidibao.net/">Gianni</a>)

= .9.9.5.5 =
* Fix notices when in debug mode
* Replaced extend functions with associative arrays 
* Some caching

= .9.9.5.4 =
* Inline CSS moved to external stylesheet
* CSS minification

= .9.9.5.3 =
* SSL - HTTPS support
* Button will display automatically below posts (by default) only after the_title has been called
 * Avoids premature calling from miscellaneous the_content filters from themes and other plugins
 * Thesis theme workaround
* French translation (by Julien)
* Belarusian translation update (by <a href="http://pc.de/" target="_blank">Marcis G</a>)

= .9.9.5.2 =
* W3C XHTML validation fix (invalid from .9.9.5)
* Removed Mozilla-proprietary opacity, which supported opacity before Firefox 1.0
* Moved Microsoft's filter CSS property to conditional inline stylesheet for validation
* Template code blocks require significantly less code
 * Note: If you're using a template code to add the icons or 'icons + button' to another area of your theme, updating the template code is recommended (see FAQ)
* Updated FAQ
 * Template code updated
* Updated template code in admin
* Switched API URL to customize URL in admin
* Fixed unbracketed noscript tag in admin
* Brazilian Portuguese translation (by <a href="http://www.raocubo.com.br/" target="_blank">Rodolfo Rodrigues</a>)

= .9.9.5.1 =
* Fixed issue with excerpts (JavaScript text output within excerpts since .9.9.5)
* Button(s) will now display below excerpts (per the fix above)
* Replaced depreciating add/remove action functions with synonymous filter functions

= .9.9.5 =
* Important note: If you are using the AddToAny Subscribe plugin, be sure to update that plugin to version 9.8+
* Faster menu initialization
* Switched to AddToAny's <a href="http://www.addtoany.com/blog/new-menu-api-examples-documentation/">new API spec</a>
* Fixed localization
* Also no longer outputs language strings if WordPress locale is set to "en" (or the default "en_US")
* Updated AddToAny icon

= .9.9.4.9 =
* New params to customize the shared link and title for buttons/icons placed in theme file(s)
 * See plugin FAQ for template code
* Ensure line break within feeds by enclosing AddToAny in a paragraph tag
* Offer CSS code in the settings panel to copy & paste into a stylesheet
* Increase opacity on standalone icons by 10%
* New standalone services
 * Google Buzz (using Google Reader endpoint - automatic update to come)
 * Blip
 * Grono
 * Wykop

= .9.9.4.8 =
* Inline CSS toggle
 * If turned off, copy the CSS to your theme's stylesheet
* Provided a link on plugin page to AddToAny's privacy policy
* Pro tip: Sharing to Google Reader right now is equivalent to sharing to Google Buzz
 * You can enable Google Reader as a standalone service
 * Google Reader is included in AddToAny's smart menu
* New standalone services
 * Balatarin
 * Vodpod
 * IWIW
 * Svejo
 * Oknotizie
 * Favoriten
 * Hatena
 * NUjij
 * Read It Later
 * diHITT
* Removed standalone service
 * Global Grind
* Japanese translation (by <a href="http://wpxtreme.jp/" target="_blank">kz</a>)

= .9.9.4.7 =
* Just a slight rename
 * Spaces in "Add to Any" removed, now camel-case: "AddToAny"
* Clarified FAQ entries
* Updated translations
* Fixed some fuzzy translations 

= .9.9.4.6 =
* Updated standalone service URIs and code names
* Renamed service codes; must re-add if you have selected
 * Ask.com
 * Gmail
 * Identi.ca
 * Mozillaca
* New standalone services
 * Box.net
 * Orkut
 * Posterous
* Removed standalone service
 * Furl
 * Yahoo! MyWeb

= .9.9.4.5 =
* Fixing standalone services admin panel because version .9.9.4.4 broke this feature
 * Standalone services are available for WordPress version 2.6+

= .9.9.4.4 =
* Backwards compatibile to version 2.0
 * Fixes PHP is_tag error
 * Fixes PHP wp_enqueue_script error in settings panel

= .9.9.4.3 =
* Automatic support for over 50 languages
 * The drop-down menu automatically detects the visitor's set language and localizes accordingly
* Less JavaScript output; removed redundant code
 * No longer outputs language strings if WordPress locale is set to the default "en_US"
* Forward support for WordPress 2.9

= .9.9.4.2 =
* Updated button text
* Further localized strings

= .9.9.4.1 =
* Twitter icon included in standard sharing button
* Albanian translation (by <a href="http://www.romeolab.com/" target="_blank">Romeo Shuka</a>)
* Romanian translation (by <a href="http://www.stefandes.com/" target="_blank">Stefan</a>)

= .9.9.4 =
* Custom standalone service support
* Custom icon support
* Updated Twitter icon

= .9.9.3.5 =
* New standalone services
 * DailyMe
 * Google Reader
 * Mozillaca
 * NewsTrust
 * Plurk
 * PrintFriendly
 * WordPress
* Fixed bug affecting certain standalone services
 * Identi.ca
 * Bookmarks.fr
 * Ask.com MyStuff
* Catalan translation update (Robert Buj)
* Clarified when template code is appropriate

= .9.9.3.4 =
* Use button IMG instead of background-image for button without text
* Defaults to 171 x 16px button

= .9.9.3.3 =
* Left-padding for icon+text link changed from 39px to 30px
* Text-index for parent UL reset
* Output buffering replaced
* Fixed admin action link
* Russian translation (by Elvis)

= .9.9.3.2 =
* Clarified button placement and theme editing
* Arabic translation

= .9.9.3.1 =
* Fix for possible global/object variable confusion with themes

= .9.9.3 =
* Add service icons
* Changelog markup update

= .9.9.2.9 =
* Removed extra character from button querystring
* New standalone services
 * Amazon Wish List
 * Blogger
 * Evernote
 * Folkd
 * Identi.ca
 * Instapaper
 * Meneame
 * Netvouz
 * TypePad

= .9.9.2.8 =
* Translations

= .9.9.2.7 =
* Updated standalone services and icons

= .9.9.2.6 =
* CSS changed to support more themes
* Admin UI updated for 2.8
 * Slightly cleaner UI
 * Includes template code for both button and standalone links (previously only found in FAQ)

= .9.9.2.5 =
* Removed dragability of dummy image in standalone services list

= .9.9.2.4 =
* Alt attribute added to standalone service image
* Title attribute added to standalone service link
* Selected standalone services in admin are more distinguishable
* Italian translation (by <a href="http://gidibao.net/">Gianni</a>)
* i18n folder renamed to languages due to a problem with the CodeStyling Localization plugin
* Contrast improvements to Open Share Icon

= .9.9.2.3 =
* Support for themes that do not support modern Loop methods
 * Permalinks now targeted for these older themes
* AddToAny URI scheme gives precedence to link URL parameter, then Title
* Sitename & Siteurl parameters deprecated for WP (they are usually redundant)

= .9.9.2.2 =
* Fixed display when all standalone services are removed in admin
* Services label renamed Standalone Services for clarity
* Updates to Danish translation
* Added Belarusian translation (by <a href="http://pc.de/" target="_blank">Marcis G</a>)

= .9.9.2.1 =
* Feed icons shown inline, no longer displayed in unordered list

= .9.9.2 =
* Services array output fixes

= .9.9.1 =
* Add services.php (critical fix)

= .9.9 =
* NEW: Individual service links!
 * Drag & Drop interface with preview
* .addtoany_share_save_container is now `<div>`, not `<p>`
* AddToAny button now contained within `<ul><li>`

= .9.8.9.2 =
* Buttons include Facebook icon
* Catalan i18n

= .9.8.9.1 =
* Automatic localization/i18n
* Rename Spanish POT to proper
* Fixed "Display Share/Save button at the bottom of pages" option when "Display Share/Save button at the bottom of posts is disabled"

= .9.8.9 =
* wp_footer() detection
* Replaced short form of PHP's open tags with long form to work around configurations with shortopentag disabled
* Spanish translation (by <a href="http://pablo.poo.cl/" target="_blank">Pablo</a>)

= .9.8.8.4 =
* Settings panel submits to current page instead of unreliable REQUEST_URI which can omit querystring on IIS
 * See http://www.microsoft.com/downloads/results.aspx?freetext=954946

= .9.8.8.3 =
* Option "Display Share/Save button at the bottom of posts on the front page" applies to all pages that can contain multiple posts

= .9.8.8.2 =
* Fix button appearing in category list view despite setting

= .9.8.8.1 =
* Refine conditionals
* Highlight admin notices
* Danish translation (by <a href="http://wordpress.blogos.dk/" target="_blank">Georg</a>)

= .9.8.8 =
* Now customize the optional text next to the 16 x 16px icons

= .9.8.7.3 =
* Important syntax fix

= .9.8.7.2 =
* Additional options / JavaScript API clarification
* i18n update 

= .9.8.7.1 =
* Text-only button stripslashes

= .9.8.7 =
* Removes unnecessary inline styling in feeds per W3C recommendation

= .9.8.6.9 =
* Compressed Open Share Icon

= .9.8.6.8 =
* Chinese translation updated

= .9.8.6.7 =
* i18n
* Chinese translation
* Installation clarified

= .9.8.6.6 =
* Open Share Icon
* WordPress 2.7 admin styling
* Settings link on Plugins page
* Basename var

= .9.8.6.5 =
* Less JavaScript redundancy from Additional Options (saves bandwidth)
* Compressed PNGs added, select a button from settings to begin using PNG (saves bandwidth)

= .9.8.6.4 =
* Additional Options in Admin panel provides link to JavaScript API
* Option to have full addtoany.com legacy page open in a new window

= .9.8.6.3 =
* Replaced short form of PHP's open tags with long form to work around configurations with short_open_tag disabled

= .9.8.6.2 =
* Current page title + blog title are used if called outside The Loop

= .9.8.6.1 =
* Fixed buttons if WordPress files are in a subdirectory while the blog appears in the site root
 * For example: http://codex.wordpress.org/Giving_WordPress_Its_Own_Directory

= .9.8.6 =
* Fixed output buffering - button should appear below posts again if option is set

= .9.8.5 =
* Button targets the current page if called outside The Loop
* Accomodates renamed plugin directory

= .9.8.4 =
* Fixed a small syntax error (critcal if you're on .9.8.3)

= .9.8.3 =
* Language & localization update
 * "After clicking OK," removed from the Bookmark tab

= .9.8.2 =
* Event attributes removed (JS now takes care of button events)
 * This eliminates the chance of errors prior to JS fully loading

= .9.8.1 =
* Fixed repo problem

= .9.8 =
* JavaScript removed from blog feed
* Option to display button (without menu) or to not display it at all below posts in blog feed
* Replaced some UTF-8 encoding functions with core WordPress functions
* For XHTML validation, special characters are converted to HTML entities within JavaScript variables
* Reprioritized plugin to load later
* Text-only button option

= .9.7 =
* Internationalization
* Buttons updated

= .9.6 =
* Moved external JavaScript to bottom so that content is prioritized over HTTP requests to static.addtoany.com
 * Please note that some improperly-coded themes may prevent this from working. See the FAQ entry for "Why isn't the drop-down menu appearing?" if this is the case.

= .9.5.2 =
* Fixed bug in Internet Explorer 6 that caused custom buttons to have a height and width of 0
* Removed the XHTML deprecated `name` attribute from the button's anchor

= .9.5.1 =
* Fixed 1 line to support those without short_open_tag

= .9.5 =
* New: Custom buttons (specify a URL)
* Fix to permit XHTML Strict validation

= .9.4 =
* New Menu Styler lets you customize the color of the menus
* New Menu Option: "Only show the menu when the user clicks the Share/Save button"
* New: Set custom JavaScript variables for further customization
* Better support for CSS styling: .addtoany_share_save
* PHP support for short_open_tag
* PHP4 legacy and compatibility fixes