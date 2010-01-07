=== Stray Random Quotes ===
Contributors: ico@italyisfalling.com
Donate link: http://code.italyisfalling.com/donate
Tags: quotes, random, widget, sidebar, AJAX, random quotes, random words, quotations, words, multiuser, randomness, shortcodes
Requires at least: 2.3
Tested up to: 2.8
Stable tag: 1.9.9

Display and rotate random quotes and words everywhere on your blog. Easy to custom and manage. Multiuser. Ajax enabled.

== Description ==

Stray Random Quotes helps you collect and display random quotes everywhere on your blog. The plugin is so flexible that it can be used to display random words of all sorts: taglines, "leave a response" messages, footer or header sections etc.
The main features:

* As many **widgets** as you need, each with its individual set of options, to display one or more quotes from all or some categories, randomly or in sequence, with or without AJAX, etc.
* **AJAX** automation so a reader of the blog can get another quote without reloading the page.
* Optional **automatic rotation** of the quotes within a given interval of seconds.
* **Multiuser** ready (contributors to the blog can access a limited version of the plugin, adding and managing their own sets of quotes)
* **Shortcodes** that can be used to add one quote or series of quotes to your posts and pages. The shortcodes come with a set of individual options as well and, if needed, they can be extended to apply everywhere on the blog, allowing random words for the tagline, the category names, the post titles etc.
* **Template tags** to add one or more quotes -- random words in general -- directly to the template pages. Template tags support many variables as well.
* A **Settings page** to customize the appearance of the quotes with little or no knowledge of HTML.
* A easy to use **management page** where even thousands of quotes can be handled easily, with bulk actions to change category, delete quotes and toggle visibility of many quotes at a time.
* A **bookmarklet** to create quotes on the fly as you browse the web and find text worth quoting.
* A **help page** where everything you need to know is explained.

See [more cool things you can do with Stray Random Quotes](http://code.italyisfalling.com/cool-things-you-can-do-with-stray-random-quotes/).

== Screenshots ==

1. How the management page works.
2. How to add a new quote.
3. A random quote in the sidebar.
4. Bulk editing in the Management page.
5. The bookmarklet in the Tools page.
6. The Settings page.
7. The widget options.

== Installation ==

1. Upload the content of stray-quotes.zip to a dedicated folder in your `/wp-content/plugins/` directory.
2. Activate the plugin on the 'Plugins' page in WordPress.
3. Stray Random Quotes has its own menu. Check the overview page in "Quotes" > "Overview". All the rest will come naturally.

_Please note:_: If you are not automatically upgrading via Wordpress, always **deactivate the older version** first and **delete the old 'stray-quotes' folder**. It is not normally necessary to backup the quotes in the database unless so advised in the changelog or on the [plugin feed](http://code.italyisfalling.com/feed/).

== Changelog ==

= 1.9.9 =

* Changed: the way locale files are loaded
* Changed: the way the settings page has to check validity of the URLs (hopefully more accurate now)
* Changed: it is now *not* mandatory to include the variables in author or source links on the settings page
* Added: chapter in the Help page dedicated to solving potential HTTPS problems (thanks to Andy for helping with this).
* Fixed: replacement of `&` char in the links (thanks to Ian for pointing this out)

= 1.9.8 =

* Fixed: a small bug caused contributors not to be considered when using AJAX.

= 1.9.7 =

* Added: when contributors are allowed to add and manage quotes, it is now possible to specify whether a widget, a shortcode or a tag should display quotes only from a given contributor. See the help page for more.
* Changed: Widget layout.
* Fixed: the link buttons for the author and source fields on the edit page would not work.

Read the complete changelog [here](http://www.italyisfalling.com/stray-random-quotes).

== Credits ==

* For Multi-widget functionality, [Millian's tutorial](http://wp.gdragon.info/2008/07/06/create-multi-instances-widget/)
* For help in developing user-end AJAX functionality, [AgentSmith](http://www.matrixagents.org)

== Localization ==

* German, thanks to Markus Griesbach
* Chinese, thanks to WGMking
* Croatian, thanks to [Rajic](http://www.atrium.hr/)
* Danish, thanks to [Georg](http://wordpress.blogos.dk/)

Actually, these translations are not updated to the latest version.
I am looking for new localizers, all languages welcome!

_Please note:_ the best way to **submit new or updated translations** is to include a direct link to the localization files in a comment to [this post](http://code.italyisfalling.com/stray-random-quotes#comments). This way the files are made available to the users sooner, and without waiting for a new release.

_Please note:_ If you want to create a localized copy of Stray Random Quotes, consider skipping the help page and translate the rest. This will save you quite some time. The help page has a lot of text.