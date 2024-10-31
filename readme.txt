=== Redbrick Digital Core ===
Contributors: RedbrickDigital, alexdemchak
Tags: Review Engine, Reputation Management, Reviews
Requires at least: 3.5.1
Tested up to: 5.8.1
Stable tag: 1.2.3
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Bring your **Review Engine** reviews into your WordPress website via shortcodes and widgets.

== Description ==

This plugins allows you to bring **Review Engine** reviews into your WordPress site via shortcodes and widget.

You **must** have a Review Engine account to use this plugin.

== Installation ==

1. Upload the plugin files to the `/wp-content/plugins/plugin-name` directory, or install the plugin through the WordPress plugins screen directly.
2. Activate the plugin through the 'Plugins' screen in WordPress
3. Go to the RBD Core menu item and place your review engine URL into provided text input.
4. Now you may use the Review Engine Shortcode on any Page or Post or use Review Engine, Review Slider, and Social Proof Widgets.

== Screenshots ==

1. Creating a Review Engine Shortcode for a page, post, or custom post type.

2. Using the Review Slider widget in a sidebar widget area.

== FAQ ==

"**I don't see the shortcode button or widget, where are they?**"

Make sure you've put a Review Engine URL into the *RBD Core* admin page!

== Changelog ==
= 1.2.1 =
* Fix division by 0

= 1.2 =
* added review source icons to review titles

= 1.0.2 =
* Fixed issues with Subdirectory URLs when calling the Review Engine API

= 1.0.1 =
* The Review Engine Display shortcode has been radically improved.
	* Bootstrap (Cutestrap) has been removed entirely.
	* The Columns layout has been updated to CSS Grid. Flex Grid is built in as well (but not currently utilized)
	* The Header has been changed to include a Review Breakdown.
	* Read More now asynchronously loads the additional review content.
	* The "Read More Reviews" button is now a "Load More Reviews" button that asynchronously loads reviews.
* A new widget has been added that utilizes the Review Engine Display shortcode features.
* The Review Slider widget has been radically improved.
	* Animation has been smoothed by dropping reliance on jQuery, unslider, and range slider.
	* Speed options now have a number in seconds instead of a range slider percent.
* The Social Proof Widget has been radically improved.
	* No more themes.
	* The Aggregate Score and Review Totals now speak for themselves without being verbose.
* General Improvements.
	* We use SVG icons in place of alt-code text based stars.
	* There's no longer dependence on any jQuery plugins, or even jQuery itself.
	* Removed dependence on Bootstrap.
	* Increase animation smoothness.
	* Utilize Ajax/XHR requests to load Review Engine API data asynchronously.


= 0.9.6.2 =
* Verification kept failing for some reason. That has been temporarily disabled.

= 0.9.6.1 =
* Allow the file `cutestrap.css` to be disabled on themes it interferes with.

= 0.9.6 =
* Added a new theme to the Widget "Social Proof" called Minimal, for a sleeker look.
* Performs a Verification Check to make sure insallers have access to a Review Engine.
* Performs a check to see if Advanced Review Funnels are enabled.

= 0.9.4 =
* Finalized removal of Schema to comply with Google's standards.
* Enabled Gravatars on reviews by default, if applicable.
* Added the started phase of a HIPAA Compliant mode. This mode forcefully removes Gravatars and replaces names with "Anonymous" and includes a simple pop-up on hover explaining it's for HIPAA Compliance.

= 0.9.3.3 =
* Minor modifications to layouts.
* Removed Schema to comply with Google's new standards of marking up Third Party Reviews.

= 0.9.3.2 =
* Changed Box-Sizing to allow proper column layouts in Cutestrap.

= 0.9.3.1 =
* Shrunk Bootstrap 4.0.0-alpha.3 into new Cutestrap.

= 0.9.3 =
* Replaced Cutestrap (based on Bootstrap 3) with Bootstrap 4, Alpha 3.

= 0.9.2 =
* Replaced file_get_contents() for API calls with custom function that uses cURL. Sites not on our server cluster were freezing on API calls.

= 0.9.1.1 =
* Enabled basic debug features via query string. "debug=api / url=[URL HERE], debug=transient url=[URL HERE] delete=true, debug=phpinfo"

= 0.9.1 =
* Broke Write a Review and Show More Reviews buttons, now fixed.

= 0.9.0.1 =
* Backwards Compatibility for Review Engine Shortcode "Per Page" parameter

= 0.9.0 =
* Refactored Widgets into sub-directories
* Wrapped all review based widgets and shortcodes in Schema Markup
* Made Review Engine Display transient salt much less stupid.

= 0.8.9 =
* Some under-the-hood changes to plugin layout and commenting have increased standards.
* Added Schema Markup to the Review Engine Display shortcode.
* Increased compatibility with Upper Right widget area.
* Increated compatibility with theme colors.
* Changed display styles of all Review Engine Display and Review Slider buttons.
* Note: This is a temporary release, and is not mandatory. If you'd like Schema on all widgets, please wait for the 0.9.0 update.

= 0.8.3 =
* An additional commit, also updated ReadMe.txt's additional section.

= 0.8.2.1 =
* Fixed RBD Core menu item not showing up on non-multisite installations.

= 0.8.2 =
* Enabled image assets, included Social Proof widget.
* TODO: Make all Review items (Shortcodes and Widgets) follow Schema.

= 0.8.1 =
* Official Beta Release
* Prevented unpublished URLs from being returned in API calls.
* Converted to Review Engine API v2
* Unlocked more options in the Shortcode and Widget

= Upgrade Notice =

= 0.9.4 =
* Upgrade to latest version is urgent and strongly advised.

= 0.9.3.3 =
* Please upgrade to the latest version.

= 0.9.3.2 =
* Please upgrade to the latest version.

= 0.9.3.1 =
* Please upgrade to the latest version.

= 0.9.3 =
* Please upgrade to the latest version.

= 0.9.2 =
* Please upgrade to the latest version.

= 0.9.1.1 =
* Please upgrade to the latest version.

= 0.9.1 =
* Please upgrade to the latest version.

= 0.9.0.1 =
* Please upgrade to the latest version.

= 0.9.0 =
* Please upgrade to the latest version.

= 0.8.9 =
* Please upgrade to the latest version.

= 0.8.3 =
* Please upgrade to the latest version.

= 0.8.2.1 =
* Please upgrade to the latest version.

= 0.8.2 =
* Please upgrade to the latest version.

= 0.8.1 =
* Please upgrade to the latest version.

== Plugin Initialization ==

To enable the shortcodes and widgets, first go to the RBD Core menu item that's in the left-hand admin menu. Place the URL of your Review Engine in the Review Engine URL text input, and then hit the Save Changes button.

##Configuring the Review Engine Display Shortcode/Widget

Now go into any Page, Post, or Custom Post Type that has a content editor. You'll now have an Add Review Engine Display button next to Add Media. Click on that to access the shortcode editing popup.

**URL**: Place the desired Review Engine URL here.

**Threshold**: Set this to 3, 4, or 5 stars to grab published reviews at and above that threshold.

**Service**: Choose which service category to grab reviews from, default "All".

**Staff**: Query against reviews with this staff member, default "All".

**Location**: Query against reviews from this location, default "All".

**Limit Characters**: Choose how many characters will show before the Read More link appears (if above the character limit).

**Reviews Per Page**: Choose how many reviews show before the Show More Reviews button appears (if you have more reviews than are returned).

**Max Columns**: Determine how many columns of reviews there are.

**Hide X**: Choose to hide some Meta Data.

**Disable 3d**: Will disabled the 3d hover effects.

*Click the Insert Into {Post Type} button and then click publish or update.*

##Configuring the Review Slider Widget

In your Appearance > Widgets page, there is a Review Slider widget. Drag that into the widget area you'd like to display the slider on. You may have more than one slider on a page if you wish.

**Title**: Give the widget an optional title.

**Review Engine URL**: Your Review Engine's URL. *Note: This is predefined, but can be changed.*

**Threshold**: Set this to 3, 4, or 5 stars to grab published reviews at and above that threshold.

**Review Count**: Choose how many reviews will be in the slider.

**Character Count**: Choose how many characters will show before the Read More link appears (if above the character limit).

**Service**: Choose which service category to grab reviews from, default "All".

**Staff**: Query against reviews with this staff member, default "All".

**Location**: Query against reviews from this location, default "All".

**Hide X**: Hide the relevant meta data

**Slider Speed**: Will adjust the slider's speed, showing each slider for the desired amount of seconds.