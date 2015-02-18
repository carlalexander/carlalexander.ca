=== Authy Two Factor Authentication ===
Contributors: authy, ethitter
Tags: authentication, authy, two factor, security, login, authenticate
Requires at least: 3.0
Tested up to: 3.9
Stable tag: 2.5.4
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin adds Authy two-factor authentication to WordPress.

== Description ==
Authy helps you proctect your WordPress site from hackers using simple two-factor authentication.

You can get your free API key at [www.authy.com/signup](https://www.authy.com/signup).

Plugin development is found at https://github.com/authy/authy-wordpress.

== Installation ==

1. Get your Authy API Key at [www.authy.com/signup](www.authy.com/signup).
2. Install the plugin either via your site's dashboard or by downloading the plugin from WordPress.org and uploading the files to your server.
3. Activate the plugin through the WordPress Plugins menu.
4. Navigate to **Settings -> Authy** to enter your Authy API keys.

== Frequently Asked Questions ==

= How can an user enable two-factor authentication? =
The user should go to his or her WordPress profile page and add his or her mobile number and country code.

= How can a user disable Authy after enabling it? =
The user should return to his or her WordPress profile screen and disable Authy at the bottom.

= Can an Admin can select specific user roles that should authenticate with Authy two-factor authentication? =
Yes, as an admin you can go to the settings page of the plugin, select the user roles in the list, and click "Save Changes" to save the configuration.

= How can the admin an admin force Authy two-factor authentication on a specific user? =
As an admin, you can go to the users page. Then, select the user in the list, and click edit. Go to the bottom, enter the user's mobile number and country code, and click "Update user."

== Screenshots ==
1. Authy Two-Factor Authentication page.

== Changelog ==

= 2.5.4 =
* Fixed the login styles for WordPress 3.9.
* Fix the login url action when the hidden backend option is enabled in a security plugin.

= 2.5.3 =
* Fixed the include of color-fresh.css file, the file was renamed to colors.css on WordPress 3.8
* Added translations for spanish language.

= 2.5.2 =
* Encode the values on query before to sending to Authy API

= 2.5.1 =
* Improved settings for disable/enable XML-RPC requests.
* Fix error message: Missing credentials, only display when the user tries to verify an authy token without signature.

= 2.5 =
* Improved the remember me option in the user authentication.
* Use manage_option capability for display the plugin settings page.

= 2.4 =
* Use the remember me option when authenticate the user.

= 2.3 =
* Hide the authy settings page for other users except for super admin (multisite)

= 2.2 =
* Hide some digits of the cellphone.

= 2.1 =
* Added missing images.

= 2.0 =
* Refactor code
* The admin can now force a user to enable Authy on next login.

= 1.3 =
* Display API errors when try to register a user.

= 1.2 =
* Fix update user profile and verify SSL certificates.

= 1.1 =
* Fix reported issues and refactor code.

= 1.0 =
* Initial public release.
