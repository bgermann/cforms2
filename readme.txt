=== cformsII - contact form ===
Contributors: bgermann, olivers
Donate link: https://www.betterplace.org/organisations/tatkraeftig/donations/new
Tags: contact form, ajax, contact, form, input, comments, post, sidebar, spam, admin
Requires at least: 3.5
Tested up to: 4.0
Stable tag: trunk
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0

cformsII is the most customizable, flexible & powerful ajax supporting contact form plugin (& comment form)!

== Description ==

This is a fork of cformsII, a highly customizable, flexible and powerful form builder plugin, covering a variety of use cases and features from attachments to multi form management, you can even have multiple forms on the same page!

The original author does not seem to further develop the plugin. This fork is an effort to keep it up to date.
If you want to use non-GPL versions older than 10.2, you can [browse the original Trac](https://plugins.trac.wordpress.org/browser/cforms/).
If you want to use plugin versions older than 14.6.3, you should rename the directory containing the plugin from "cforms2" to "cforms".

= Credits =

Some icons are based on the wonderful set of Jan Kovařík: [glyphicons](http://glyphicons.com).

Oliver, the original author, believes in good karma, so it is up to you to activate the link attribution on the global settings page. Thanks for the consideration.

= License Information =

Copyright (c) 2006-2012 Oliver Seidel (email : oliver.seidel @ deliciousdays.com)

Copyright (c) 2014      Bastian Germann

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.


== Installation ==

= Installing the plugin =

You can install the plugin via Wordpress Dashboard. It should show up by searching
for cforms2. If this does not work for you, there should be an option to upload a
[zip file](https://downloads.wordpress.org/plugin/cforms2.zip).

If you want to install manually, please upload the complete plugin folder "cforms2",
contained in the zip file, to your WP plugin directory!

= Upgrading the plugin =

If you want to upgrade from the original cformsII from deliciousdays.com, please
upgrade to the original version 14.6 first, make sure you upgraded your settings and
backup your database. Then deactivate the original plugin and install this fork.

If you want to delete the original version, make sure you do not delete your upload
directory.

= Did you modify any cforms theme (CSS) files, images etc? =

Create a custom folder under your WP plugin directory & call it "cforms-custom". Move
all your custom files into it. This folder will protect your changes going forward.

Also note that it is always advisable to make or keep a backup of your
current plugin directory just in case you need to revert back to a
previous release!

Check out the [cforms CSS Guide and webcast](http://www.deliciousdays.com/cforms-forum/css-styling-and-layout/) on layout customization.


== Frequently Asked Questions ==

Please visit the [cforms plugin forum](http://www.deliciousdays.com/cforms-forum) for old [FAQ](http://www.deliciousdays.com/cforms-forum/troubleshooting/frequently-asked-questions-faqs/) and more help.

= How can I contribute code =

Please use [GitHub pull requests on our mirror](https://github.com/bgermann/cforms2/pulls).


== Screenshots ==

Please visit the [cforms plugin page](http://www.deliciousdays.com/cforms-plugin) for screenshots & user examples.


== Upgrade Notice ==

= 14.6.8 =
There are full German and Italian translations available now.

= 14.6.7 =
This is only compatible with Wordpress 3.5+. Version 3.3 and 3.4 support is dropped.

= 14.6.3 =
The Wordpress function wp_mail is now used for mails, which makes use of built-in PHPMailer. If you want to configure it to use an external SMTP server, use an appropriate plugin, e.g. [WP Mail SMTP](https://wordpress.org/plugins/wp-mail-smtp/).

= 14.5 =
v14.5+ is only compatible with WP 3.3+

= 10.6 =
WP 2.8 compliance

= 10.2 =
GPL compliance!

= 9.0 =
v9+ introduces a completely new method of storing its settings, **please make a WP database backup first (!)** and then migrate your settings (cforms will guide you)!
Then commence with a manual upgrade, including proper plugin deactivation and reactivation, followed by the guided data migration.

= 8.2 =
MAJOR CHANGES for ajax support; *cforms.js* doesn't have to be edited anymore. (ok, there is maybe a 1% that you do have to) Please do not replace new cforms.js with your old, customized cforms.js!

= 8.0 =
WP 2.5 compatibility

= 5.52 =
bugfix release

= 5.51 =
bugfix release

= 5.0 =
The extra settings for form ID's (in email messages) are obsolete, this feature is now available via the default variable {ID} - see Help!
The special input field "Email subject" is now obsolete, since both message body and SUBJECT fully support default and custom variables

= 4.8 =
bugfixes mostly

= 4.7 =
bugfixes only

= 4.6 =
bugfixes & features

= 4.5 =
maintenance, bug fixes and enhancements

= 4.1 =
features

= 4 =
feature & bugfix

= 3.5 =
as part of the install/update either deactivate/reactive the plugin or delete the existing Tracking tables, to make use of the new table structure

= 2 =
After updating please edit each form on your plugins config page to verify that the email field is checked with "Is Email" to ensure email verification


== Donations ==

This fork is originally developed for the website of [tatkräftig](http://tatkraeftig.org) (only German), a German charitable organization that encourages and supports people in social engagement.
If you can afford it, please consider [making a donation](https://www.betterplace.org/organisations/tatkraeftig/donations/new) to support that organization and further development.

The original author who developed cforms until 2012 also has a [donation page](http://www.deliciousdays.com/cforms-donation).


== Roadmap ==

Some things are to be done:

* filter user input
* make attachments download support directories that are not exported via HTTP
* [support for Subscribe to Comments Reloaded](https://wordpress.org/support/topic/suggestion-support-subscribe-to-comments-reloaded)
* long term: refactoring with object oriented approach
