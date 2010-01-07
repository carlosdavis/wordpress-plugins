=== Advanced Excerpt ===
Contributors: basvd
Tags: excerpt, advanced, post, posts, template, formatting
Donate link: http://sparepencil.com/code/advanced-excerpt/
Requires at least: 2.2
Tested up to: 2.7
Stable tag: trunk

Several improvements over WP's default excerpt. The size of the excerpt can be limited using character or word count, and HTML markup is not removed.

== Description ==

This plugin adds several improvements to WordPress' default way of creating excerpts.

1. It can keep HTML markup in the excerpt (and you get to choose which tags are included)
2. It trims the excerpt to a given length using either character count or word count
3. You can customise the excerpt length and the ellipsis character that will be used when trimming
4. The excerpt length is *real* (everything belonging to HTML tags is not counted)
5. Possibility to ignore custom excerpts and use the generated one instead
6. Theme developers can use `the_advanced_excerpt()` for even more control (see the FAQ)

In addition to keeping HTML markup in the excerpt, the plugin also corrects HTML that might have been broken due to the trimming process.

Version 0.2.1 adds support for multibyte characters (eg. Chinese and Japanese). This is slightly experimental, more details in the FAQ.
Plugin translations are fully supported and language files are included for translation. The FAQ provides more info on this, also.

== Installation ==

After you've downloaded and extracted the files:

1. Upload the complete `advanced-excerpt` folder to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to 'Excerpt' under the 'Options' tab and configure the plugin


== Frequently Asked Questions ==

= Why do I need this plugin? =

The default excerpt created by WordPress removes all HTML. If your theme uses `the_excerpt()` to view excerpts, they might look weird because of this (e.g. smilies are removed, lists are flattened, etc.) This plugin fixes that and also gives you more control over excerpts.

= Does it work for WordPress version x.x.x? =

I haven't had the chance to test the plugin on many versions of WordPress. It has been tested on 2.2 through 2.7, but it might work on other versions, too. You can safely try it yourself, because the plugin is unlikely to break anything (it's only an output filter). Please let me know if you succesfully tested it on another version of WordPress.

= Is this plugin available in my language? / How do I translate this plugin? =

The plugin comes bundled with a few languages. The correct language will automatically be selected to match your [WordPress locale](http://codex.wordpress.org/WordPress_in_Your_Language).

More information on translation will be added in the future.

= Does this plugin support multibyte characters, such as Chinese? =

First of all, it should be noted that word-based excerpt length only works if your language uses normal whitespace as a word separator. If you use another language, you have to uncheck the *Use words?* option.

PHP's support for multibyte characters is not perfect. The plugin provides support for these characters to the best of its ability, but there are no guarantees that everything will work.
Your best bet is to use UTF-8 encoding (which WordPress uses by default). If you still encounter problems, check with your host if the *mbstring* PHP extension is enabled on your server.

= Can I manually call the filter in my WP templates, for example? =

The plugin automatically hooks on `the_excerpt()` function and uses the parameters specified in the options panel.

If you want to call the filter with different options, you can use `the_advanced_excerpt()` template tag provided by this plugin. This tag accepts [query-string-style parameters](http://codex.wordpress.org/Template_Tags/How_to_Pass_Tag_Parameters#Tags_with_query-string-style_parameters) (theme developers will be familiar with this notation).

The following parameters can be set:

* `length`, an integer that determines the length of the excerpt
* `use_words`, if set to `1`, the excerpt length will be in words; if set to `0`, characters will be used for the count
* `no_custom`, if set to `1`, an excerpt will be generated even if the post has a custom excerpt; if set to `0`, the custom excerpt will be used
* `ellipsis`, the string that will substitute the omitted part of the post; if you want to use HTML entities in the string, use `%26` instead of the `&` prefix to avoid breaking the query
* `exclude_tags`, a comma-separated list of HTML tags that must be removed from the excerpt
* `allow_tags`, a comma-separated list of HTML tags that are allowed in the excerpt

A custom, advanced excerpt call could look like this:

`the_advanced_excerpt('length=320&use_words=0&no_custom=1&ellipsis=%26hellip;&exclude_tags=img,p,strong');`

= Does this plugin work outside the Loop? =

No, this plugin fetches the post from The Loop and there is currently no way to pass a post ID or anything custom of that kind to it.
You can, however, consider to [start The Loop manually](http://codex.wordpress.org/The_Loop#Multiple_Loops).
