=== Zip Lookup for MailChimp ===
Contributors: yikesinc, eherman24, liljimmi, yikesitskevin
Donate link: http://www.yikesinc.com
Tags: MailChimp, Yikes MailChimp, Zip, Address, Zip Lookup
Requires at least: 3.8
Tested up to: 4.9.8
Stable tag: 1.1.3
License: GPL-3.0+
License URI: http://www.gnu.org/licenses/gpl-3.0.html

This add-on extends Easy Forms for MailChimp to lookup and populate city/state/country based on a user-supplied US zip code.

== Description == 

This add-on extends Easy Forms for MailChimp by leveraging HERE's Geocode API to retrieve address data based on a user's zip code. Users simply type in a zip code and the [hidden] city, state, and country fields will automatically be filled in and submitted to MailChimp. The plugin will take effect on all forms with 'Address' fields assigned to them.

> Note: This add-on plugin requires [Easy Forms for MailChimp](https://wordpress.org/plugins/yikes-inc-easy-mailchimp-extender/) to work.

== Installation ==

1. Download the plugin .zip file and make note of where on your computer you downloaded it to.
2. In the WordPress admin (yourdomain.com/wp-admin) go to Plugins > Add New or click the "Add New" button on the main plugins screen.
3. On the following screen, click the "Upload Plugin" button.
4. Browse your computer to where you downloaded the plugin .zip file, select it and click the "Install Now" button.
5. After the plugin has successfully installed, click "Activate Plugin" and enjoy!

== Frequently Asked Questions ==

**All documentation can be found in [our Knowledge Base](https://yikesplugins.com/support/knowledge-base/us-zip-lookup-for-easy-forms-for-mailchimp/).**

= Do I need a another plugin for this to work? =
Yes, this plugin is an add-on to [Easy Forms for MailChimp](https://wordpress.org/plugins/yikes-inc-easy-mailchimp-extender/).

= Are there any settings for this plugin? =
There are no settings for this add-on, the plugin just works automatically after it is installed and activated.

== Screenshots ==

1. A form with the address field but without the add-on
2. A form with the address field with the add-on

== Changelog ==

= Zip Lookup for MailChimp 1.1.3 =
* Changed from Google's Geocoding API to HERE's Geocoding API
* The default country for searching is the USA. To change this, use the `yikes-mailchimp-zip-lookup-country` filter.

= Zip Lookup for MailChimp 1.1.1 =
* Changes to keep this add-on in sync with the base Yikes MailChimp plugin
* Changes to put this plugin on the official WordPress.org plugin repo, such as i18n functionality, function renaming, and readme updates

= Zip Lookup for MailChimp 1.0 =
* Initial Release
