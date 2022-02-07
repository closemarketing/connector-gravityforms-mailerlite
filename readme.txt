=== Connector GravityForms and MailerLite ===
Contributors: closemarketing, davidperez, sacrajaimez
Tags: genesis, widgets
Donate link: https://close.marketing/go/donate/	
Requires at least: 4.0
Tested up to: 5.9
Stable tag: 1.5
Version: 1.5

This plugin connects GravityForms with MailerLite.

== Description ==
[GravityForms](https://close.marketing/go/gravityforms/) plugin gives you the posibility to create forms in a very simple way. It has a lot of addons that connects GravityForms with online services, but [MailerLite](https://close.marketing/go/mailerlite/) is not in that list.

[We are experts in Mailerlite!](https://www.mailerlite.com/experts/closemarketing)

Only with your API Key from MailerLite you will connect every form to MailerLite, in order to keep all your subscribers in MailerLite. 

Steps: 
- Go GravityForms > Settings > MailerLite. Put your MailerLite API Key that is in integrations tab.
- Once the API is valid, you will have to go to every form that needs to connect to MailerLite.
- In every Form > MailerLite > Create new feed. You will have to map every form field to MailerLite fields.

That's all! Every user that fills that form, It will upload lead to MailerLite. 


Closemarketing plugins for Forms:
- [FormsCRM](https://wordpress.org/plugins/formscrm/)
- [Others](https://profiles.wordpress.org/closemarketing/#content-plugins)


Others Closemarketing Premium Plugins:
- [Close.technology](https://en.close.technology/products-online/plugins-wordpress/?utm_source=WordPress.org)


== Installation ==

Extract the zip file and just drop the contents in the wp-content/plugins/ directory of your
WordPress installation and then activate the Plugin from Plugins page.


== Developers ==
[Official Repository Github](https://github.com/closemarketing/connector-gravityforms-mailerlite)

== Changelog ==
= 1.5 =
*	Refactoring connection API with native WordPress method.
*  Fix conflict with library. Removed MailerLite's library.
*  Fixed resubscribed option.

= 1.4.1 =
*	Fixed fatal error with WooMailerLite.

= 1.4 =
*	Fixed activation in multisite.
*	Updated libraries.
*	PHPCS Fixes.

= 1.3.3 =
*     Fixed name MailerLite.

= 1.3.2 =
*     Removed Appsero library.
*     Updated Mailerlite SDK.
*     Detects GravityForms installed.

= 1.3.1 =
*     Fixed fatal error.

= 1.3 =
*     Fixed fatal error is_plugin_active.
*     Tested WordPress 5.4.
*     Fixed Grouplist not getting in Feeds.

= 1.2 =
*     Fixed Conflict with WooCommerce – MailerLite
*     Updated libraries: ralouphie/getallheaders (2.0.5 => 3.0.3), guzzlehttp/psr7 (1.5.2 => 1.6.1), php-http/message (1.7.2 => 1.8.0), and php-http/discovery (1.6.1 => 1.7.0)

= 1.1 =
*     Error not Mapping fields.
*	Libraries updated.
  
= 1.0 =
*     First released.

== Links ==

*     [Closemarketing](https://www.closemarketing.net/)
