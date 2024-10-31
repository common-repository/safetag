=== Safetag ===
Contributors: sourcetop
Tags: IAB Tagging
Requires at least: 5.0
Tested up to: 6.6.1
Stable tag: 2.1.6
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Safetag ensures brand safety by scanning site content for negative keywords provided by advertisers, optimizing ad targeting and maximizing inventory.

== Description ==

Safetag helps publishers meet brand safety requirements by scanning all posts for negative keyword lists provided by advertising partners.

= Manage Individual Campaign Lists to Maximize Inventory =

The Safetag WordPress plugin ensures brand safety for advertisers by scanning each piece of site content identifying matches against negative keyword lists provided by advertisers for ad campaigns.

Safetag exposes each campaign as key value pairs to be used for ad targeting. Because each campaign is managed separately, ad op teams can more intelligently manage their ad inventory.

Campaigns can be set to exclude content, but also to include posts with specific terms. For instance, advertisers can target all articles that contain a list of celebrity names or clothing brands that match their audience profile.

We know there is considerable overlap between keyword lists and Safetag builds up its own index of terms with each new Campaign building on top of the previous ones, greatly speeding up time to deploy.

When new posts are created, or old ones edited, Safetag will trigger a fresh scan of that post so nothing slips through.

Publishers can confidently assure their ad partners their ads are in a brand safe environment with Safetag.

= Detailed Reporting =

Brands and agencies send generic keyword lists for their campaigns as required by lawyers. However these lists are generally a hodgepodge of terms and phases added over time with little or no understanding of the impact to individual sites. The downside is a lot of valuable inventory is banned for no rational reason.

Safetag reporting gives publishers the ammunition they need to have an intelligent conversation to adjust a negative keyword list resulting in more inventory – a win for both parties.

Internally these reports can also inform the editorial team the words and phrases most often banned giving them feedback on what to avoid – or at least understand the impact of using particular language. “Click bait” headlines may drive traffic, but if it cannot be monetized it’s a wasted opportunity.

= IAB TAGGING =

Add IAB Audience tags to the site and IAB 4 Content tags to each piece of content to further enhance targeting or programmatic revenue.

= FREE VERSION =

Users can create one campaign with up to 500 terms for free to scan all posts with detailed reporting.

= Export Tags for Headless setup =

In a headless setup, you can export tags using the REST API. (‘yourdomain/wp-json/safetag-api/v1/post-campaign/post_id’).
Note: This URL needs to be secured.

== Installation ==

= Minimum Requirements =

* PHP version 5.6 or greater (PHP 7.2 or greater is recommended)

= Automated Installation =

1. Download, install and activate through the WP Admin panels plugin directory
2. Enjoy!

Or...

= Manual Installation =

1. Upload the entire `/safetag` directory to the `/wp-content/plugins/` directory.
2. Activate Safetag through the 'Plugins' menu in WordPress.

== Frequently Asked Questions ==

= Can I get more than one free campaign? =

No. Safetag allows one free campaign for one site. Users can pay for a site license for unlimited campaigns. Discounts are provided for multiple sites. Please visit safetag.ai/pricing/ for more information.

= How long does it take to scan content? =

The first list takes the longest as it needs to scan all posts for all keywords. We have run campaign lists with over 10,000 terms over 20,000+ posts and it takes about four hours. Safetag keeps an index so additional lists take much less time. Most lists overlap and only new terms are scanned.

= What happens when I create a post? =

Safetag scans all new posts when they are saved in the background and adds the post to the master index.

= What happens when I edit a post? =

Safetag does an independent scan when a post is edited to ensure no new terms are added and reindexes the post.

== Screenshots ==
1. Safetag Campaigns
2. Safetag Exclude
3. Safetag Exclude All
4. Safetag Include
5. Safetag Posts
6. Safetag Settings
7. Safetag Right Rail

== Changelog ==
= 2.1.6 =
* A custom user role called Safetag has been added.
= 2.1.5 =
* Validate the uniqueness of the campaign name
= 2.1.4 =
* Bug fixing.
= 2.1.3 =
* Bug fixing.
= 2.1.2 =
* Bug fixing.
* Post-type selection settings have been added, where users can select a specific post types to scan.
= 2.1.1 =
* Bug fixing.
= 2.1.0 =
* Added cron event alert
= 2.0.9 =
* Added outgoing GAM call
= 2.0.8 =
* Bug fixing.
= 2.0.7 =
* Bug fixing.
= 2.0.6 =
* Campaign edit/save success message update
= 2.0.5 =
* Display an expiration warning message on the settings page when licenses have expired or have less than 30 days remaining.
* Display a warning message if attempting to update over 500 keywords while using the free version license.
* In the free version license, users are only able to edit the most recent campaign.
* Updated FAQ on plugin dashboard
= 2.0.4 =
* Updated Screenshots and Description
= 2.0.3 =
* Latest WordPress version support.
= 2.0.2 =
* The Report navigation color change when the page is active.
= 2.0.1 =
* UI/UX Updates
* Improvement in Keyword Scan
* Updated Audience Tag
= 1.1.7 =
* Error handle
= 1.1.6 =
* Error handle
= 1.1.5 =
* UI/UX Updates
* Improvement in Keyword Scan
* Updated Audience Tag
= 1.1.3 =
* Updated admin dashboard styling
= 1.1.2 =
* Resolved global style-breaking issue
= 1.1.1 =
* Export tags using the REST API for headless setup
= 1.0.9 =
* class-safetag-public.php added empty check
= 1.0.7 =
* safetag-public.js put this file for wp_localize_script

= 1.0.6 =
* updated iab tags
* updated iab audience tags
* set transient for cache

= 1.0.4 =
* Update Admin Dashboard
= 1.0.4 =
* Updated IAB audience Tag
= 1.0.3 =
* Some minor bug fix
= 1.0.2 =
* Updated Readme.md
* Added report chart
* Added IAB Audience Tags

= 1.0.1 =
* Initial Release
