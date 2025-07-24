=== Primary Redirect ===
Contributors: handyplugins, m_uysl
Tags: multisite, login, redirect, redirection, primary
Requires at least: 5.0
Tested up to: 6.8
Requires PHP: 7.4
Stable tag: 2.0.1
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Redirects users to a custom URL or their primary blog's dashboard after login, replacing the default WordPress behavior.

== Description ==

Primary Redirect is a powerful WordPress plugin that allows you to customize where users are redirected after logging in. Instead of the default WordPress behavior, you can redirect users to:

* A custom URL of your choice
* Their primary blog's dashboard (in multisite installations)

This plugin is perfect for:
* Multisite networks where you want users to go to their primary blog
* Sites with custom dashboards or landing pages
* Improving user experience with personalized redirects

= Key Features =

* **Custom URL Redirect**: Set any URL as the post-login destination
* **Primary Blog Redirect**: Automatically redirect users to their primary blog's dashboard (multisite)
* **Network & Single Site Support**: Works on both multisite networks and single WordPress sites
* **Easy Configuration**: Simple settings interface in WordPress admin
* **Developer Friendly**: Clean, modern code following WordPress best practices

= Multisite Support =

On multisite installations, you get additional options:
* Redirect users to their primary blog's dashboard
* Network-wide settings that apply to all sites
* Override custom URLs with primary blog redirect

= Single Site Support =

On single WordPress sites, configure a custom redirect URL that applies to all users after login.

== Installation ==

1. Upload the plugin files to `/wp-content/plugins/primary-redirect/` directory, or install through WordPress admin
2. Activate the plugin through the 'Plugins' screen in WordPress
3. For multisite: Configure settings in Network Admin > Settings
4. For single site: Configure settings in Settings > General

== Frequently Asked Questions ==

= Does this work with single WordPress sites? =

Yes! The plugin works on both single WordPress installations and multisite networks.

= Can I redirect different users to different URLs? =

Currently, the plugin redirects all users to the same custom URL or their primary blog dashboard. User-specific redirects are not supported in this version.

= Does this affect interim logins or authentication flows? =

No, the plugin respects WordPress's interim login and reauth processes and won't interfere with them.

= Is this compatible with other login plugins? =

The plugin uses WordPress's standard `login_redirect` filter, so it should be compatible with most other plugins. However, if another plugin also modifies login redirects, the last one to run may take precedence.

== Screenshots ==

1. Network admin settings for multisite installations
2. Single site settings in General options

== Changelog ==

= 2.0 (Jul 24, 2025) =
* Bump tested up to WordPress 6.8

= 2.0 (Jul 24, 2025) =
* Complete rewrite with modern WordPress best practices
* Improved security with proper input sanitization and capability checks
* Better code organization and documentation
* Updated plugin header and branding for HandyPlugins
* Improved user interface with better descriptions
* Added proper URL validation
* Enhanced multisite support
* Minimum WordPress version: 5.0
* Minimum PHP version: 7.4

= 1.1 =
* Serbo-Croatian language pack added

= 1.0 =
* Initial release

== Upgrade Notice ==

= 2.0 =
Major update with improved security, modern code, and better user experience. Please test in a staging environment before updating production sites.
