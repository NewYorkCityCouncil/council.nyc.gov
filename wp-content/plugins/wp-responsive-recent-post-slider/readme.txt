=== WP Responsive Recent Post Slider/Carousel ===
Contributors: wponlinesupport, anoopranawat, pratik-jain, piyushpatel123, ridhimashukla, patelketan, mehulwpos, jahnavwponlinesupport
Tags: post slider, posts slider, recent post slider, recent posts slider, slider, responsive post slider, responsive posts slider, responsive recent post slider, responsive recent posts slider, WordPress posts slider, post slideshow, posts slideshow, recent posts slideshow, shortcodes, wponlinesupport
Requires at least: 4.0
Tested up to: 6.0.2
Stable tag: 3.1
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Add and display Responsive WordPress Recent Post Slider and Carousel on your website with 4 designs (Slider) and 1 designs (Carousel) using a shortcode. Also added Gutenberg block support.

== Description ==

[Explore Post Slider/Carousel Features](https://demo.essentialplugin.com/recent-post-slider-demo/?utm_source=WP&utm_medium=RecentPostSlider&utm_campaign=Read-Me) | [Annual or Lifetime Bundle Deal](https://www.essentialplugin.com/pricing/?utm_source=WP&utm_medium=RecentPostSlider&utm_campaign=Read-Me)

Attractive & eye-catching [Post Slider and Carousel](https://www.essentialplugin.com/wordpress-plugin/wp-responsive-recent-post-slider/?utm_source=WP&utm_medium=RecentPostSlider&utm_campaign=Read-Me).

This plugin displays your recent WordPress posts using :

* Posts Slider (4 designs)
* Posts Carousel (1 designs)

= Features : =
[youtube https://www.youtube.com/watch?v=BmCpbC9lXd4]

When you want to makeover your WordPress website theme with something extraordinary and creative, you must consider WP responsive recent post slider. Help your website to get a slide-wise display to show the recent posts/news. Not just eye appealing, it is also loved by the visitors as they find it quite easy to locate the recent posts. 

Display unlimited number of WordPress posts slider and carousel in a single page or post with different sets of options like category, limit, navigation type.

**Also added Gutenberg block support.** 

= Here is the plugin shortcode example =

**Post Slider** 

<code>[recent_post_slider]</code>

**Post Carousel** 

<code>[recent_post_carousel]</code>

**To display only latest 4 post:**

<code>[recent_post_slider limit="4"]</code>
Where limit define the number of posts to display. You can use same parameter with Carousel shortcode.

**If you want to display Recent Post Slider by category then use this short code:** 

<code>[recent_post_slider category="category_ID"]</code>
You can use same parameter with Carousel shortcode.

**We have given 4 designs. For designs use the following shortcode:**

<code>[recent_post_slider design="design-1"]</code> 
Where designs are : design-1, design-2, design-3, design-4. You can use same parameter with Carousel shortcode but in Carousel we have given only 1 design i.e. design-1.

= Here is Template code =
<code><?php echo do_shortcode('[recent_post_slider]'); ?> </code>
<code><?php echo do_shortcode('[recent_post_carousel]'); ?> </code>

= Use Following Recent Post Slider parameters with shortcode =
<code>[recent_post_slider]</code>

* **limit** : [recent_post_slider limit="8"] (Display only 8 latest post. By default it display 8 latest posts with shortcode [recent_post_slider]. If you want to display all posts then use limit="-1").
* **design** : [recent_post_slider design="design-1"] (You can select 4 design( design-1, design-2, design-3, design-4 ) for your recent post slider. )
* **category**: [recent_post_slider category="category_ID"] ( i.e. Display recent post slider by their category ID ).
* **show_category_name** : [recent_post_slider show_category_name="true" ] (Display category name OR not. By default value is "True". Options are "true OR false").
* **show_date** : [recent_post_slider show_date="false"] (Display post date OR not. By default value is "True". Options are "true OR false")
* **show_content** : [recent_post_slider show_content="true" ] (Display post Short content OR not. By default value is "True". Options are "true OR false").
* **media_size** : [recent_post_slider  media_size="full"] (where you can use values : thumbnail, medium, medium_large, large and full)
* **Pagination and arrows** : [recent_post_slider dots="false" arrows="false"]
* **Auto play and Auto play Interval**: [recent_post_slider autoplay="true" autoplay_interval="100"]
* **Slide Speed**: [recent_post_slider speed="3000"]
* **content_words_limit** : [recent_post_slider content_words_limit="30" ] (Control post short content Words limit. By default limit is 20 words).
* **post_type:** [recent_post_slider post_type="post"] (i.e. added custom post type support where you add custom post. By default value is "post")
* **taxonomy:** [recent_post_slider taxonomy="category"] (i.e. added custom taxonomy support where you add custom taxonomy. By default value is "category")
* **posts:** [recent_post_slider posts="1,2,3"] (i.e. include some posts with their post-id that you want to display)
* **hide_post:** [recent_post_slider hide_post="1,2,3"] (i.e. exclude some posts with their post-id that you do not want to display)
* **show_author:** [recent_post_slider show_author="false"] (i.e. Display author name OR not. By default value is "true". Values are "true OR false")
* **show_read_more:** [recent_post_slider show_read_more="true"] (Display read more button. Values are "true OR false")
* **rtl:** [recent_post_slider rtl="true"] (for rtl mode. By default value is “false”. Options are “true OR false”).
* **lazyload:** [recent_post_slider lazyload="ondemand"] (Lazy load images. Defaults to ''. Two options can be passed. 'ondemand', 'progressive')

= Use Following Recent Post Carousel parameters with shortcode =
<code>[recent_post_carousel]</code>

* **limit** : [recent_post_carousel limit="8"] (Display only 8 latest post. By default it display 8 latest posts with shortcode [recent_post_carousel]. If you want to display all posts then use limit="-1").
* **design** : [recent_post_carousel design="design-1"] (You can select 1 design for your recent post carousel. ).
* **category**: [recent_post_carousel category="category_ID"] ( i.e. Display recent post carousel by their category ID ).
* **show_category_name** : [recent_post_carousel show_category_name="true" ] (Display category name OR not. By default value is "True". Options are "true OR false").
* **show_date** : [recent_post_carousel show_date="false"] (Display post date OR not. By default value is "True". Options are "true OR false")
* **show_content** : [recent_post_carousel show_content="true" ] (Display post Short content OR not. By default value is "True". Options are "true OR false").
* **media_size** : [recent_post_carousel  media_size="full"] (where you can use values : thumbnail, medium, medium_large, large and full)
* **slides_to_show** : [recent_post_carousel slides_to_show="3" ] (Display number of posts at a time. By default value is "3").
* **slides_to_scroll** : [recent_post_carousel slides_to_scroll="1" ] (Scroll number of posts at a time. By default value is "1").
* **Pagination and arrows** : [recent_post_carousel dots="false" arrows="false"]
* **Auto play and Auto play Interval**: [recent_post_carousel autoplay="true" autoplay_interval="100"]
* **Slide Speed**: [recent_post_carousel speed="3000"]
* **content_words_limit** : [recent_post_carousel content_words_limit="30" ] (Control post short content Words limit. By default limit is 20 words).
* **post_type:** [recent_post_carousel post_type="post"] (i.e. added custom post type support where you add custom post. By default value is "post")
* **taxonomy:** [recent_post_carousel taxonomy="category"] (i.e. added custom taxonomy support where you add custom taxonomy. By default value is "category")
* **posts:** [recent_post_carousel posts="1,2,3"] (i.e. include some posts with their post-id that you want to display)
* **hide_post:** [recent_post_carousel hide_post="1,2,3"] (i.e. exclude some posts with their post-id that you do not want to display)
* **show_author:** [recent_post_carousel show_author="false"] (i.e. Display author name OR not. By default value is "true". Values are "true OR false")
* **show_read_more:** [recent_post_carousel show_read_more="true"] (Display read more button. Values are "true OR false")
* **rtl:** [recent_post_carousel rtl="true"] (for rtl mode. By default value is “false”. Options are “true OR false”).
* **lazyload:** [recent_post_carousel lazyload="ondemand"] (Lazy load images. Defaults to ''. Two options can be passed. 'ondemand', 'progressive')

= Pro Features include: =
> <strong>Premium Version</strong><br>
>
> * Recent Post Slider with 25 designs.
> * Recent Post Carousel with 30 designs.
> * Recent Post grid box slider with 8 designs.
> * Shortcode <code>[recent_post_carousel]</code> and <code>[gridbox_post_slider]</code>
> * 3 Widgets (Post slider, Post List/Slider-1, Post List/Slider-2).
> * V1.3.4 Support WordPress default post as well as any custom post-type and taxonomy .
> * Gutenberg Block Supports.
> * Visual Composer Support.
> * Gutenberg, Elementor, Beaver and SiteOrigin Page Builder Native Support (New).
> * Divi Page Builder Native Support (New).
> * Fusion Page Builder (Avada) Native Support (New).
> * WP Templating Features.
>
> [Explore Post Slider/Carousel Pro Features](https://www.essentialplugin.com/wordpress-plugin/wp-responsive-recent-post-slider/?utm_source=WP&utm_medium=RecentPostSlider&utm_campaign=Read-Me)
>

= Features include: =
* Added Gutenberg block support.
* Post slider
* Post carousel
* Easy to add.
* Also work with Gutenberg shortcode block. 
* Given 4 designs.
* Media size i.e.  thumbnail, medium, medium_large, large and full
* Responsive.
* Responsive touch slider.
* Mouse Draggable.
* You can create multiple post slider with different options at single page or post.
* Custom post type support.
* Exclude Post with their ID's that you do not want to display.

= How to install : =
[youtube https://www.youtube.com/watch?v=Bpi8f1JEBzc] 

= Privacy & Policy =
* We have also opt-in e-mail selection, once you download the plugin , so that we can inform you and nurture you about products and its features.

== Installation ==

1. Upload the 'wp-recent-post-slider' folder to the '/wp-content/plugins/' directory.
2. Activate the "wp-recent-post-slider with Widget" list plugin through the 'Plugins' menu in WordPress.
3. Add this short code where you want to display recent post slider
<code>[recent_post_slider]</code>

= How to install : =
[youtube https://www.youtube.com/watch?v=Bpi8f1JEBzc] 

== Screenshots ==

1. Design-1
2. Design-2
3. Design-3
4. Design-4
5. Also work with Gutenberg shortcode block. 
6. Category shortcode for post
7. Gutenberg Block
8. Gutenberg Block Support

== Changelog ==

= 3.1 (15, Sep 2022) =
* [*] Update - Use escaping and sanitize functions for better security.
* [*] Update - Update demo and documentation link.
* [*] Update - Update Slick slider JS to stable version 1.8.0
* [*] Update - Check compatibility to WordPress version 6.0.2
* [*] Fix - Fixed slider initialize issue in Elementor tab.
* [*] Fix - SEO & HTML validation error of empty image source when plugin lazy load is enabled.
* [*] Fix - Fixed some typo mistake.
* [*] Remove - Removed unnecessary files, code and images.

= 3.0.9 (17, May 2022) =
* [*] Tested up to: 6.0

= 3.0.8 (29, March 2022) =
* [+] Added free vs pro functionality.
* [+] Tested up to: 5.9.2

= 3.0.7 (16, March 2022) =
* [+] Added demo link
* [-] Removed some unwanted code and files.

= 3.0.6 (11, Feb 2022) =
* [-] Removed some unwanted code and files.

= 3.0.5 (03, Feb 2022) =
* [*] Tested up to: 5.9 
* [*] Solved Gutenberg wp-editor widget issue.

= 3.0.4.1 (15, Dec 2021) =
* [*] Minor fix.

= 3.0.4 (12, Nov 2021) =
* [*] Fix - Resolve Gutenberg WP-Editor script related issue. 
* [*] Update - Add some text and links in read-me file.

= 3.0.3 (25, Oct 2021) =
* [*] Updated - Updated some important links.

= 3.0.2.1 (16, Sep 2021) =
* [*] Fixed a small issue when using with Divi theme

= 3.0.2 (15, Sep 2021) =
* [*] Tested up to: 5.8.1
* [*] Updated Demo Link.

= 3.0.1 (18, Aug 2021) =
* [*] Updated language file and JSON.

= 3.0 (17, Aug 2021) =
* [*] Updated all external links
* [*] Tweak - Code optimization and performance improvements.
* [*] Fixed Blocks Initialize issue.

= 2.7.1 (31, May 2021) =
* [*] Tested up to: 5.7.2
* [*] Added - HTTPS link in our analytics code to avoid browser security warning.

= 2.7 (15, March 2021) =
* [*] Tested up to: 5.7

= 2.6 (25, Jan 2021) =
* [+] New - Added native shortcode support for Elementor, SiteOrigin and Beaver builder.
* [+] New - Added Divi page builder native support.
* [+] New - Added Fusion Page Builder (Avada) native support.
* [*] Tweak - Code optimization and performance improvements.

= 2.5.1 (27, Oct 2020) =
* [*] Minor  Update - Fixed conflict from tgmpa (theme plugin recommends - if your theme using tgmpa library) where it was showing message inside "How It Works - Display and shortcode"

= 2.5 (21, Oct 2020) =
* [+] New - Click to copy the shortcode from the getting started page.
* [*] Update - Regular plugin maintenance. Updated read me file.
* [*] Added - Added our other Popular Plugins under Recent Post Slider --> Install Popular Plugins From Us. This will help you to save your time during creating a website.

= 2.4 (14, Aug 2020) =
* [*] jQuery( document ).ready(function($) is replaced with function( $ ) to solve the issue with 3rd party plugin and theme js error.

= 2.3.3 (14-07-2020) =
* [*] Follow WordPress Detailed Plugin Guidelines for Offload Media and Analytics Code.

= 2.3.2 (06, July 2020) =
* [*] Regular plugin maintenance. Updated read-me file. Tested plugin with some more popular themes. 

= 2.3.1 (08, May 2020) =
* [+] New - Added 'lazyload' shortcode parameter for all slider shortcodes. Now you can able to set lazy loading in two different method lazyload="ondemand" OR lazyload="progressive".
* [+] Update - Minor change in CSS and JS.
* [*] Template File - Minor template file has been updated. If you have override template file then verify with latest copy.

= 2.3 (13, April 2020) =
* [+] New - Added Gutenberg block support. Now use plugin easily with Gutenberg!
* [+] New - Added 'align' and 'extra_class' parameter for slider shortcode. Now both slider shortcode are support twenty-nineteen and twenty-twenty theme Gutenberg block align and additional class feature.
* [*] Tweak - Code optimization and performance improvements.

= 2.2.5 (24, Jan 2020) =
* [*] New : Added new shortcode parameter "posts" for both shortcode.

= 2.2.4 (26, Dec 2019) =
* [*] Tested : Tested with Gutenberg blocks.
* [*] Updated features list.
* [*] Added unique prefix to all classes to avoid the conflict with any theme and third-party plugins.  
* [*] Fixed some CSS issues.

= 2.2.3 (09, Sept 2019) =
* [*] Fixed title line height issue reported by a user.

= 2.2.2 (20, August 2019) =
* [+] Added : Added esc_url() for featured image in a design file.
* [*] Fixed title duplicate issue reported by a user.

= 2.2.1 (2, August 2019) =
* [+] Added : Added a new shortcode for post carousel with a design.
* [*] New Shortcode : [recent_post_carousel]
* [+] Added : wp_reset_postdata();

= 2.1 (22, June 2019) =
* [*] Fixed : Fixed read more button hover issue in all designs.

= 2.0.2 (28, Jan 2019) =
* [*] Fixed an error (Error: Undefined variable: content) when content not added in the editor and excerpt is also empty. Thanks @franfellipe2 for showing us this issue.

= 2.0.1 (20, Dec 2018) =
* [*] Update Opt-in flow.

= 2.0 (06-12-2018) =
* [*] Tested with WordPress 5.0 and Gutenberg.
* [*] Tested with Twenty Nineteen theme.
* [*] Fixed some CSS issues.

= 1.5 (22-09-2018) =
* [*] Fixed read more button issue.
* [*] Fixed some design related issue.

= 1.4.5 (23-08-2018) =
* [+] Added ... at the end of the content limit.

= 1.4.4 (04-06-2018) =
* [*] Fixed some minor issues.

= 1.4.3 (31-05-2018) =
* [*] Follow some WordPress Detailed Plugin Guidelines.

= 1.4.2 (08-05-2018) =
* [*] Fixed issues when featured image not added.

= 1.4.1 (10-3-2018) =
* [*] Created plugin separate menu 'Recent Post Slider' as per user's feedback.

= 1.4 (9-3-2018) =
* [+] Added the_permalink() for image under design-1, design-2 and design-3

= 1.3.5 (1-3-2018) =
* [+] Added the_permalink() for image under design-4 and taken care if image not added.

= 1.3.4 (12-01-2018) =
* [+] Added new shortcode parameter i.e. media_size="full" where you can use values : thumbnail, medium, medium_large, large and full

= 1.3.3 (09-10-2017) =
* [+] Thanks to @ciroiodice for helping to improve our plugin and we have added <code>strip_shortcodes()</code> function if user has not added content in Excerpt section and want to display content form Editor section only.
* [+] <code>strip_shortcodes()</code> function deletes all shortcode tags from the short content that are going to display in slider. 

= 1.3.2 (04-10-2017) =
* [*] Updated slick JS to latest version.
* [*] Fixed Firefox reader view.

= 1.3.1 (26-09-2017) =
* [*] Thanks to @cruisertech showing us a bug in the version 1.3 i.e. category parameter was missing.

= 1.3 (26-09-2017) =
* [+] Now use same design multiple time.
* [+] Added custom taxonomy support with custom post type.
* [*] Plugin files structure modified.
* [*] If you are using any cache plugin, kindly clear all cache after plugin update.

= 1.2.8 =
* [+] RTL Supported.

= 1.2.7 =
* [*] Fixed some CSS issue.

= 1.2.6 =
* [+] Added "Post Slider - How it work" tab.
* [-] Removed Pro design tab.

= 1.2.5 =
* Added 'show_read_more' shortcode parameter for read more button.
* Updated plugin design page.
* Updated slider JS to newer version.
* Fixed some CSS issues.

= 1.2.4 =
* Fixed some CSS issues.

= 1.2.3 =
* Fixed some CSS issues.

= 1.2.2 =
* Fixed some bugs.
* Added 3 New shortcode parameters i.e. **post_type="post", hide_post="1,2,3", show_author="false"**

= 1.2.1 =
* Fixed some bugs.

= 1.2 =
* Fixed some bugs.
* Added More designs in Pro version and fixed some bug.

= 1.1 =
* Fixed some bugs
* Added More designs in Pro version and fixed some bug.

= 1.0 =
* Initial release.