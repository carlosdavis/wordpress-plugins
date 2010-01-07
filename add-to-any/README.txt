=== Add to Any: Share/Bookmark/Email Button ===
Contributors: micropat
Donate link: http://www.addtoany.com/contact/
Tags: bookmarking, social, social bookmarking, social bookmarks, bookmark, bookmarks, sharing, share, sharethis, saving, save, Post, posts, page, pages, images, image, admin, statistics, stats, links, plugin, widget, e-mail, email, seo, button, delicious, google, digg, reddit, facebook, myspace, twitter, stumbleupon, technorati, wpmu, addtoany, add, any
Requires at least: 2.0
Tested up to: 2.8
Stable tag: 0.9.9.3.5

Help readers share, save, bookmark, and email your posts and pages using any service, such as Facebook, Twitter, Digg, Delicious and over 100 more.

== Description ==

Help readers **share**, **save**, **bookmark**, and **email** your posts and pages using **any service**, such as Facebook, Twitter, Digg, Delicious, and over 100 more social bookmarking and sharing sites. The button comes with AddToAny's customizable **Smart Menu**, which **places the services visitors use at the top of the menu**, based on each visitor's browsing history.

<a href="http://www.addtoany.com/" title="Sharing button widget" target="_blank">Share Button</a> (demo)

The E-mail tab makes it easy to share via Google Mail, Yahoo! Mail, Hotmail, AOL, and any other web-based e-mailer or desktop program. The **Add to Favorites** button (or Bookmark tab) helps users bookmark using any browser (Internet Explorer, Firefox, Chrome, Safari, Opera, etc.).

Individual **service icons** let you optimize your blog posts for specific social sites.  Choose from over 100 individual services.

* AddToAny <a href="http://www.addtoany.com/blog/smart-menus-the-services-your-visitors-use-displayed-first/" target="_blank">Smart Menu</a>
* Individual service links (like Sociable)
* Includes all services
* Menu updated automatically
* WordPress optimized, localized (English, Chinese, Spanish, Portuguese, Italian, Danish, Catalan, Russian, Belarusian)
* Google Analytics integration
* Many more publisher and user features!

See also:

* The <a href="/extend/plugins/add-to-any-subscribe/" title="WordPress RSS Subscribe widget plugin">Subscribe button</a> plugin
* The <a href="http://www.addtoany.com/buttons/for/wordpress_com" title="WordPress.com sharing button widget" target="_blank">Share button for WordPress.com</a> blogs

<a href="http://www.addtoany.com/share_save" title="Share" target="_blank">Share this plugin</a>

== Installation ==

1. Upload the `add-to-any` directory (including all files within) to the `/wp-content/plugins/` directory
1. Activate the plugin through the `Plugins` menu in WordPress

== Frequently Asked Questions ==

= How often is the list of services within the menu updated? =

Constantly... and it's done automatically without having to upgrade.

= Where can I choose which button and individual icons to display and other options? =

Go to `Settings` > `Share/Save Buttons`.

= Why isn't the drop-down menu appearing? =

It's likely because your your theme wasn't <a href="http://codex.wordpress.org/Theme_Development#Plugin_API_Hooks" target="_blank">coded properly</a>.  Using the Theme Editor, make sure that the following piece of code is included in your theme's `footer.php` file just before the `</body>` line:

`<?php wp_footer(); ?>`

= How can I move both the button and the individual icons to another area of my theme? =

In the Theme Editor, place this code block where you want the button and individual icons to appear in your theme:

`<?php echo '<ul class="addtoany_list">';
if( function_exists('ADDTOANY_SHARE_SAVE_ICONS') )
	ADDTOANY_SHARE_SAVE_ICONS( array("html_wrap_open" => "<li>", "html_wrap_close" => "</li>") );
if( function_exists('ADDTOANY_SHARE_SAVE_BUTTON') )
	ADDTOANY_SHARE_SAVE_BUTTON( array("html_wrap_open" => "<li>", "html_wrap_close" => "</li>") );
echo '</ul>'; ?>`

= How can I move just the button to another area of my theme? =

Directions are located within the plugin's settings panel located in `Settings` > `Share/Save Buttons` under `Button Placement`. In the Theme Editor, you will place this line of code where you want the button to appear in your theme:

`<?php if( function_exists('ADDTOANY_SHARE_SAVE_BUTTON') ) { ADDTOANY_SHARE_SAVE_BUTTON(); } ?>`

= How can I move just the individual icons to another area of my theme? =

In the Theme Editor, place this line of code where you want the individual icons to appear in your theme (within an HTML list):

`<?php echo '<ul class="addtoany_list">';
if( function_exists('ADDTOANY_SHARE_SAVE_ICONS') )
	ADDTOANY_SHARE_SAVE_ICONS( array("html_wrap_open" => "<li>", "html_wrap_close" => "</li>") );
echo '</ul>'; ?>`

Or you can place the icons as individual links (without being wrapped in an HTML list):

`<?php if( function_exists('ADDTOANY_SHARE_SAVE_ICONS') ) { ADDTOANY_SHARE_SAVE_ICONS(); } ?>`

= How can I force the button to appear in individual posts and pages? =

If your button isn't already set up to appear (it is by default), type the following tag into the page or post that you want the button to appear in: `<!--sharesave-->`

= How can I remove a button from individual posts and pages? =

Type the following tag into the page or post that you do not want the button to appear in: `<!--nosharesave-->`

= Why do embedded objects (like Flash) disappear when the menu is displayed? =

This is done to overcome browser limitations that prevent the drop-down menu from displaying on top of intersecting embedded objects.  If you would like to disable this, uncheck the `Hide embedded objects (Flash, video, etc.) that intersect with the menu when displayed` option on the plugin's settings page.

== Screenshots ==

1. Add to Any Share/Save button, featuring the Open <a href="http://www.shareicon.com/">Share Icon</a>
2. Drop-down menu that appears instantly when visitors use the share button
3. E-mail tab, with direct links to the most popular web-based e-mailers' auto-filled Compose page, a web-based sender for use with any e-mail address, and a link for desktop email clients
4. Settings panel
5. Color chooser for your Add to Any menus

== Changelog ==

= .9.9.3.5 =
* New standalone services
 * DailyMe
 * Google Reader
 * Mozilla.ca
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
* Sitename & Siteurl parameters depreciated for WP (they are usually redundant)

= .9.9.2.2 =
* Fixed display when all standalone services are removed in admin
* Services label renamed Standalone Services for clarity
* Updates to Danish translation
* Added Belarusian translation

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
* Add to Any button now contained within `<ul><li>`

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
* Removed the XHTML depreciated `name` attribute from the button's anchor

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