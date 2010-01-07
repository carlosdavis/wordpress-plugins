=== Plugin Name ===
Contributors: James Lao
Donate link: http://jameslao.com/
Tags: category, posts, widget
Requires at least: 2.8
Tested up to: 2.8.2
Stable tag: 2.3

Adds a widget that shows the most recent posts in a single category.

== Description ==

Category Posts Widget is a light widget designed to do one thing and do it well: display the most recent posts from a certain category.

Features:

* Support for displaying thumbnail images via [Simple Post Thumbnails plugin](http://wordpress.org/extend/plugins/simple-post-thumbnails/).
* Specify how many posts to show
* Set which category the posts should come form
* Designate how many of the widgets you need
* Specify whether to make to the widget title a link to the category page
* Optionally show the post excerpt

== Installation ==

1. Download the plugin.
2. Upload it to the plugins folder of your blog.
3. Goto the Plugins section of the WordPress admin and activate the plugin.
4. Goto the Widget tab of the Presentation section and configure the widget.

== Changelog ==

2.3

* Really tried to fix bug where wp_query global was getting over written by manually instantiating a WP_Query object

2.1

* Fixed bug where wp_query global was getting over written.

2.0

* Updated to use the WP 2.8 widget API.
* Added support for [Simple Post Thumbnails plugin](http://wordpress.org/extend/plugins/simple-post-thumbnails/).