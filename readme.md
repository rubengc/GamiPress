# GamiPress #

The most flexible and powerful gamification system for WordPress.

## Description ##

[GamiPress](https://gamipress.com "GamiPress") is the easiest way to gamify your WordPress website in just a few minutes, letting you award your users with digital rewards for interacting with your site.

Easily define the achievements, organize requirements, and choose from a range of assessment options to determine whether each task or requirement has been successfully achieved.

GamiPress is extremely powerful and infinitely extensible. Check out some of the built in features:

### Many ways to define how to award the different points and achievements ###

* Site activity (triggers based on publishing posts and pages, commenting, daily visits or logging in to your site).
* Completing specific other achievements, once or a specified number of times.
* Completing one or all achievements of a specified type.
* Points thresholds.
* Admin awarded achievements.

### Unlimited number of Points Types ###

* Create as many types of points as you like.
* Name your custom types whatever you wish (Credits, Gems, Coins, etc).
* Easily define how automatically your users will earn points using the 'Points Awards' tool.
* Management of each user points wallet.

### Unlimited number of Achievements Types ###

* Create as many types of achievement as you like.
* Name your custom types whatever you wish (Quests, Badges, etc).
* Easily define how they relate to one another using the 'Required Steps' tool.
* Set default images for each achievement type or select unique images for every achievement item.

### Drag and drop controls ###

* Simple yet powerful admin interface for defining the "Required Steps" for any achievement.
* Easily link together one or more triggers, steps or actions into the conditions needed to earn an achievement.
* Limit by period of time in which the user can complete a requirement (daily, weekly, monthly or yearly).

### Reward user progress ###

* Issue digital rewards for any combination of achievements.
* Award points for commenting, logging in, visiting or completing any combination of tasks.
* Display a congratulatory message, customizable per achievement, on each achievement page.

### Widgets, Shortcodes and Shortcode Embedder ###

* Multiple options and parameters for each widget or shortcode for greater flexibility.
* Live shortcode embedder appears in the toolbar of all WordPress content editor areas, allowing you to transform any page or post into part of your gamification system without referencing any of the shortcodes.
* Shortcode to integrate specific available achievements into any post or page of your site.
* Integrated shortcode documentation within the plugin menu.
* Just activate GamiPress and place simple shortcodes on any page or post, and you've got a gamification system running on your WordPress site!

### Powerful tools ###

* Built in tools to recount old activities, migrate plugin configuration or clean testing data.

### Log everything ###

* Flexible log system with support for public and private logs.
* Display the latest logs anywhere on your site for all users or a specific one.

### Theme Agnostic ###

* GamiPress works with just about any standard WordPress theme.
* No special hooks or theme updates are needed.
* Overwritable templates system to allow you customize everything you want through your GamiPress theme folder.
* Turn any page or post into a way to display available achievements, earned points or latest logs and for users to track their progress.

### Integrated with your favorites WordPress plugins ###

* [Easy Digital Downloads integration](https://wordpress.org/plugins/gamipress-easy-digital-downloads-integration/)
* [WooCommerce integration](https://wordpress.org/plugins/gamipress-woocommerce-integration/)
* [AffiliateWP integration](https://wordpress.org/plugins/gamipress-affiliatewp-integration/)
* [BuddyPress integration](https://wordpress.org/plugins/gamipress-buddypress-integration/)
* [Contact Form 7 integration](https://wordpress.org/plugins/gamipress-contact-form-7-integration/)
* [bbPress integration](https://wordpress.org/plugins/gamipress-bbpress-integration/)
* [Ninja Forms integration](https://wordpress.org/plugins/gamipress-ninja-forms-integration/)
* [LearnPress integration](https://wordpress.org/plugins/gamipress-learnpress-integration/)
* [Gravity Forms integration](https://wordpress.org/plugins/gamipress-gravity-forms-integration/)

### Helpful Links ###

GamiPress is made available by [Tsunoa](https://tsunoa.com/ "Tsunoa"). Here are some ways to stay connected and to see what else we are up to:

* [GamiPress.com](https://gamipress.com/ "GamiPress") - GamiPress official website
* [Add-ons](https://gamipress.com/add-ons "GamiPress Add-ons") - GamiPress official add-ons
* [Documentation](https://gamipress.com/docs "GamiPress documentation") - GamiPress online documentation
* [Contact](https://gamipress.com/contact-us "GamiPress contact") - GamiPress contact page
* [GitHub](https://github.com/rubengc/gamipress "GamiPress on GitHub") - GamiPress GitHub repository
* [Tsunoa.com](https://tsunoa.com/ "Tsunoa") - Tsunoa official website

## Installation ##

### From WordPress backend ###

1. Navigate to Plugins -> Add new.
2. Click the button "Upload Plugin" next to "Add plugins" title.
3. Upload the downloaded zip file and activate it.

### Direct upload ###

1. Upload the downloaded zip file into your `wp-content/plugins/` folder.
2. Unzip the uploaded zip file.
3. Navigate to Plugins menu on your WordPress admin area.
4. Activate this plugin.

## Screenshots ##

**Frontend shortcodes and widgets demo**
![Frontend shortcodes and widgets demo](https://ps.w.org/gamipress/assets/screenshot-1.png "Frontend shortcodes and widgets demo")

**Requirements edit screen**
![Requirements edit screen](https://ps.w.org/gamipress/assets/screenshot-2.png "Requirements edit screen")

**Live shortcode embedder**
![Live shortcode embedder](https://ps.w.org/gamipress/assets/screenshot-3.png "Live shortcode embedder")

**Builtin widgets**
![Builtin widgets](https://ps.w.org/gamipress/assets/screenshot-4.png "Builtin widgets")

**Logs edit screen**
![Logs edit screen](https://ps.w.org/gamipress/assets/screenshot-5.png "Logs edit screen")

## Frequently Asked Questions ##

#### Which shortcodes come bundled with GamiPress? ####

GamiPress comes with the following shortcodes:

* [gamipress_achievement] to display a desired achievement.
* [gamipress_achievements] to display a list of achievements.
* [gamipress_logs] to display a list of logs.
* [gamipress_point_types] to display a list of points types with their points awards.
* [gamipress_points] to display current or specific user points balance.

In your WordPress admin area, navigate to the GamiPress Help/Support menu where you can find the full list of available shortcodes, including descriptions of all parameters each shortcode supports.

#### Which widgets come bundled with GamiPress? ####

GamiPress comes with the following widgets:

* Achievement: to display a desired achievement.
* Achievements: to display a list of achievements.
* Logs: to display a list of logs.
* Points Types: to display a list of points types with their points awards.
* User Points: to display current or specific user points balance.

## Changelog ##

### 1.2.1 ###

* Fixed wrong requirement period limit check.
* Improvements on query to determine if an activity trigger has a listener.
* Fixed wrong bar check on licensing library.

### 1.2.0 ###

* Improvement: Just show multisite fields if install is multisite.
* Improvements on admin area stylesheets.
* Removed backward compatibility for [gamipress_achievement] parameters show_filter and show_search.
* Reset public changelog (moved old changelog to changelog.txt file).
* No more bugs found, so, time to release this version as stable release! :)