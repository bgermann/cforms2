=== cformsII - contact form ===
Contributors: bgermann, olivers, cbacchini, codifex
Donate link: https://www.betterplace.org/organisations/tatkraeftig/donations/new
Tags: contact form, ajax, contact, form, input, post, sidebar, spam, admin
Requires at least: 3.9
Tested up to: 4.5
Stable tag: trunk
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0

cformsII is the most customizable, flexible & powerful ajax supporting contact form plugin!

== Description ==

This is a fork of cformsII, a highly customizable, flexible and powerful form
builder plugin, covering a variety of use cases and features from attachments to
multi form management, you can even have multiple forms on the same page!

Oliver, the original author, does not further develop the plugin.
This fork is an effort to keep it up to date.
If you want to use plugin versions older than 14.6.3, you should rename the
directory containing the plugin from "cforms2" to "cforms". But bear in mind
that old versions should not be used in public systems, because they contain
known serious vulnerabilities that are exploited in the wild. The current
security baseline version is 14.11.

= Related Plugins =

cformsII has pluggable CAPTCHA support.
[Really Simple CAPTCHA for cformsII](https://wordpress.org/plugins/cforms2-really-simple-captcha)
provides an image CAPTCHA. To convert the old CAPTCHA fields to Really
Simple CAPTCHA, please install the current version and activate the plugin.

[Contact Form DB](https://wordpress.org/plugins/contact-form-7-to-database-extension)
has support for storing cformsII's submission data and exporting in more
formats than cformsII does. Therefore cformsII's database tracking will be
removed in the long run.

= Credits =

The translations that are or were distributed were provided by:

* www.alpenimmobilien.de
* Michael Lederstatter / Buy-Hosting.net
* Leadvirus
* Gianni Diurno
* Gill Ajoft
* Serge Rauber
* Cyrille Sanson-Stern
* Pedro Germani Ghiorzi
* Cátia Kitahara
* Sofia Panchenko
* [Stas Mykhajlyuk](http://kosivart.if.ua)
* Charles Tang

= License Information =

Copyright (c) 2006-2012 Oliver Seidel (email : oliver.seidel @ deliciousdays.com)

Copyright (c) 2014-2016 Bastian Germann

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

You can install the plugin via Wordpress Dashboard. It should show up by
searching for cforms2. If this does not work for you, there should be an option
to upload a zip file, which is available on the
[wordpress.org plugin directory](https://wordpress.org/plugins/cforms2/).

If you want to install manually, please upload the complete plugin folder
"cforms2", contained in the zip file, to your WP plugin directory!

If you want to check integrity of the download, please use the cforms2.*.zip
downloads and the corresponding .sig
GPG signature files that are published via
[GitHub releases](https://github.com/bgermann/cforms2/releases).
The [key used for signing](https://pgp.mit.edu/pks/lookup?op=vindex&fingerprint=on&search=0x2626D16964438E53)
has the fingerprint `D942 6F96 37DC A799 FF0F  9AF2 2626 D169 6443 8E53`.
The git tags themselves are also signed beginning with version 14.8.

Please note that cformsII depends on [PHP Session Extensions](http://php.net/manual/book.session.php),
which are enabled by default.

= Upgrading the plugin =

If you want to upgrade from the original cformsII from deliciousdays.com, please
upgrade to [version 14.6.0.5](https://plugins.svn.wordpress.org/cforms2/assets/cforms2.14.6.0.5.zip)
first, make sure you upgraded your settings (resave your global settings and every form)
and [backup your database](https://codex.wordpress.org/Backing_Up_Your_Database).
Then deactivate the original plugin and install the current version of this fork.

If you want to delete the 14.6.0.5 version by deleting directory "cforms" and
you have any cforms-uploaded files, make sure you do not delete your attachments
directory, which is contained in the cforms directory by default.

= Did you modify any cforms theme (CSS) files? =

Create a custom folder under your WP plugin directory & call it "cforms-custom".
Move all your custom files into it. This folder will protect your changes going
forward.

Check out the
[cforms CSS Guide](http://www.deliciousdays.com/download/cforms-css-guide.pdf)
on layout customization.


== Frequently Asked Questions ==

= I do not get any emails from my form. Why? =

Most probably this is not cformsII's fault. Please check your Wordpress mail
configuration with a plugin like
[Check Email](https://wordpress.org/plugins/check-email/).

= Where can I find old cformsII versions? =

You can find old versions in the
[Developers section](https://wordpress.org/plugins/cforms2/developers/).

= Where are the external SMTP settings? =

That function was removed. The Wordpress function
[wp_mail](https://codex.wordpress.org/Function_Reference/wp_mail) is used for
mails now, which makes use of built-in PHPMailer.
If you want to configure it to use an external SMTP server, use an appropriate
plugin, e.g. [WP Mail SMTP](https://wordpress.org/plugins/wp-mail-smtp/)
or [Postman SMTP Mailer](https://wordpress.org/plugins/postman-smtp/).

= I upgraded and lost my widgets. What can I do? =

Beginning with 14.6.10 Wordpress 2.8 Widget API is used for cforms widgets.
You have to recreate your widgets. You can find your old settings in debug
output on the Global Settings Admin menu.
Just search for widgets with your browser's search.

= How can I contribute code? =

Please use [GitHub pull requests](https://github.com/bgermann/cforms2/pulls).

= Where are comment and Tell-a-Friend fields? =

These features were remove with version 14.12.


== Localization ==

You can see the available languages and contribute via [GlotPress](https://translate.wordpress.org/projects/wp-plugins/cforms2).

If your language is not available, you can find a list of the
[translations for the original cforms version](http://web.archive.org/web/20141103044209/http://www.deliciousdays.com/cforms-plugin/),
which also work with this fork.
To use one of those files, please rename the cforms-?.mo file to
cforms2-?.mo and put it in the wp-content/languages/plugins directory.


== Donations ==

This fork is originally developed for the website of
[tatkräftig](http://tatkraeftig.org) (only German), a German charitable
organization that encourages and supports people in social engagement.
If you can afford it, please consider
[making a donation](https://www.betterplace.org/organisations/tatkraeftig/donations/new)
to support that organization and further development.

The original author who developed cforms until 2012 also has a
[donation page](http://www.deliciousdays.com/cforms-donation).


== Roadmap ==

Some things are to be done:

* replace jqModal with jquery-ui-dialog
* filter user input
* grunt build process similar to Wordpress core
* long term: refactoring with object oriented approach
* long term: unit tests and continuous integration


== Upgrade Notice ==

= 14.12 =
RSS support is removed. If you depend on it, please try Contact Form DB's RSS support. WordPress comment support and Tell-a-Friend support is removed, too.

= 14.11 =
CAPTCHA support is handled by the new plugin Really Simple CAPTCHA for cformsII. If you depend on CAPTCHAs, please install this plugin. Additionally, if you use my_cforms_ajax_filter(), you get a new data structure there.

= 14.9.12 =
If you run a custom css and depend on a CAPTCHA reset image, you have to remove it.

= 14.9 =
If you use my-functions.php, you have to take action (see my-functions.php.txt).

= 14.8 =
This is only compatible with Wordpress 3.9+. Version 3.5 to 3.8 support is dropped.

= 14.7 =
Explicit Subscribe To Comments support is removed. Please delete all the remaining input elements from your forms. Also check the styling on comment forms!

= 14.6.10 =
Wordpress 2.8 Widget API is now used for cforms widgets. You have to recreate your widgets.

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


== Changelog ==

= 14.12 =

* enhanced: make all date picker options configurable (breaks old datepicker settings)
* enhanced: remove role capabilities on uninstall
* other:    remove RSS support
* other:    remove Tell-a-Friend support
* other:    remove WordPress comment support

= 14.11.4 =

* bugfix:   correct AJAX nonce for submitcform action

= 14.11.3 =

* bugfix:   make the AJAX WordPress comment work again (bug was introduced with 14.11)
* bugfix:   remove the "Select: Email/Comment" field type, also known as send2author, which contained a bug since 14.6.3
* bugfix:   remove the "Extra comment notification" field type, also known as commentXnote, which contained a bug since 14.6.3
* bugfix:   also add roles if network activated in multisite
* enhanced: remove extra comment success message
* other:    replace AJAX WordPress comment forms by non-AJAX ones
* other:    do not save comment form submissions in tracking database anymore

= 14.11.2 =

* bugfix:   AJAX forms are saved in tracking database again

= 14.11.1 =
* bugfix:   make the cforms2_after_processing_action parameter contain file information not dependent on form configuration
* bugfix:   only show tinymce button when in admin

= 14.11 =
* enhanced: unify AJAX and non-AJAX processing
* enhanced: pluggable captcha API change
* enhanced: it is possible now to show more than one form with Q&A on one site
* bugfix:   make Q&A check work again in AJAX mode
* bugfix:   prevent using fake CAPTCHAs and Q&As and do not depend on MD5
* other:    my_cforms_ajax_filter() API function's data structure changed to the same as my_cforms_filter
* other:    remove CAPTCHA feature in favour of Really Simple CAPTCHA for cformsII
* other:    remove Portuguese (Brazil) translation after migration to translate.wordpress.org
* other:    deprecate my-functions API

= 14.10.1 =
* bugfix:   fix tracking form download url
* enhanced: change gettext domain from cforms to cforms2 in cforms-options.php

= 14.10 =
* other:    remove French, German, Italian, Russian and Ukrainian translations after migration to translate.wordpress.org

= 14.9.13 =
* other:    change gettext domain from cforms to cforms2

= 14.9.12 =
* bugfix:   load dashicons if not in dashboard

= 14.9.11 =
* enhanced: change CAPTCHA reset button
* enhanced: remove the old static jQuery UI theme and get current from Google Hosted Libraries
* added:    possibility to change the jQuery UI theme
* other:    remove dark-rounded theme

= 14.9.10 =
* enhanced: replace some icons with Dashicons
* enhanced: bump up jqModal to the newest version
* other:    replace jqDnR with jquery-ui-draggable
* other:    remove jQuery TextAreaResizer plugin as this is default browser behaviour with CSS 3
* other:    remove possibility to overwrite tracking fields

= 14.9.9 =
* bugfix:   do not depend on TLD consisting of only 2-4 characters, even on non-AJAX forms
* bugfix:   do not reset multipage form on back button
* enhanced: remove old picture in help section

= 14.9.8 =
* bugfix:   no duplicate form rendering

= 14.9.7 =
* bugfix:   cforms2_make_time: do not depend on timezone being a region
* bugfix:   do not depend on TLD consisting of only 2-4 characters
* bugfix:   undo problematic change from 14.9.6
* other:    remove possibility to show JavaScript alert messages

= 14.9.6 =
* enhanced: make PHP 5.2 compatible again (in 14.9.4 one 5.3 function was introduced)
* enhanced: on style settings do not include ../.. in request parameters to not cause a false positive in application firewalls
* other:    always show HTML5 field types in form settings

= 14.9.5 =
* bugfix:   circumvent fatal error on wrong form date settings

= 14.9.4 =
* bugfix:   remove warning message after form submission
* bugfix:   set static datepicker format on forms options page
* other:    remove ClockPick

= 14.9.3 =
* bugfix:   settings are not removed anymore on plugin activation
* bugfix:   make HTML5 checkbox have the right status (global settings)
* bugfix:   do not depend on slash in request string to check for admin page (WordPress 4.2 incompatibility)

= 14.9.2 =
* bugfix:   eliminate scattered > and missing labels
* bugfix:   fix corrupt settings page path

= 14.9.1 =
* enhanced: convert Q&A feature to the new pluggable captcha API
* added:    pluggable captcha API

= 14.9 =
* enhanced: change the my-functions API to be an extra plugin
* bugfix:   Shortcode puts the form to the right place
* bugfix:   missing import in cforms-options.php
* other:    remove basic style editor

= 14.8.2 =
* enhanced: elimination of notices in debug mode
* enhanced: better mail handling
* added:    introduce WP_DEBUG_CFORMS2 constant for debug messages
* added:    check for upload_max_filesize
* bugfix:   Content-Transfer-Encoding and Content-Type separation
* other:    remove donation button in help section
* other:    remove extra Date, MIME-Version and X-Priority headers
* other:    remove email priority settings

= 14.8.1 =
* bugfix:   fix warning introduced with last version's remote code execution fix
* added:    Russian translation
* other:    clean mail handling

= 14.8 =
* feature:  add Shortcode to insert forms
* bugfix:   fix remote code execution via file upload
* enhanced: make TinyMCE plugin localization part of gettext
* enhanced: use TinyMCE 4 API and redesign TinyMCE form insertion
* added:    Brazilian Portuguese and French translations

= 14.7 =
* enhanced: replace Subscribe To Comments support with a more general approach to also support Subscribe To Comments Reloaded
* added:    Bosnian, Croatian, Serbian, Simplified Chinese and full Ukrainian translations
* other:    remove old WP Ajax Edit Comments support, because current versions work without special support

= 14.6.11 =
* enhanced: API function my_cforms_action() can throw an exception
* bugfix:   several AJAX related JavaScript undefined function bugs introduced in 14.6.8
* bugfix:   repair non-AJAX comment form
* other:    remove support for old Comment Luv versions

= 14.6.10 =
* enhanced: use Wordpress 2.8 Widget API
* bugfix:   JavaScript error on flexigrid drag event
* bugfix:   show errors on sending mail
* bugfix:   mitigate some possible SQL injection attacks
* other:    remove attribution link, because the original cforms site is shut down

= 14.6.9 =
* bugfix:   [Download Entries on Tracking not working](https://wordpress.org/support/topic/download-entries-on-tracking-not-working)
* other:    cut old changelog because of rendering problems with too big readme.txt file on wordpress.org

= 14.6.8 =
* enhanced: full German and Italian translations
* enhanced: use jquery-ui-sortable instead of its ancient Interface Elements predecessor
* added:    store version in database again
* added:    migration for month and day names

= 14.6.7 =
* enhanced: reorganize JavaScript files to provide better dependency management
* enhanced: bump up ClockPick to the newest version
* enhanced: bump up jqModal to the newest version
* enhanced: use jQuery wrapper for md5.js
* enhanced: replace the color picker with iris (wordpress standard since 3.5)
* feature:  uninstallation deletes cforms database content
* bugfix:   captcha preview in global settings renders correctly
* other:    remove kibo dependency
* other:    removed unused images and code (including [GPL incompatible](http://www.dynamicdrive.com/notice.htm), stolen JavaScript code)
* other:    replace donation button

= 14.6.6 =
* bugfix:   wrong AJAX MIME type
* added:    guideline to upgrade from original cforms
* other:    removed old upgrade code
* other:    moved some additional information in textfiles to readme.txt

= 14.6.5 =
* other:    reintroduced the public insert_cform function

= 14.6.4 =
* bugfix:   hanging form submission

= 14.6.3 =
* feature:  Use the ____Plugin_Localization directory to store and distribute some GPL translations
* enhanced: unobfuscate JavaScript changes from version 10.3 on and kick out js_src.zip which was not updated since then
* enhanced: Wordpress function wp_mail is used for mails and direct PHPMailer support is removed
* enhanced: make use of the native Wordpress AJAX features
* enhanced: get rid of the data.tmp and abspath.php files
* enhanced: get rid of some paths and urls in the database
* enhanced: bump up flexigrid to the newest version
* other:    replaced the captcha fonts with GPL fonts
* other:    reintroduced attribution link, that is opt-in now, which is demanded by the wordpress.org plugin guidelines
* other:    prefix function names and class names with cforms2, which is demanded by wordpress.org
* other:    remove the custom plugin update check
* other:    remove of a lot of old code which is not needed for Wordpress 3.3+
* other:    remove v13 update information
* other:    move and rework changelog to be Markdown-compatible and have a tag subset

= 14.6.2 =
* bugfix:   issues upgrading from older versions of cforms
* other:    removed link at the bottom of the contact form

= 14.6.1 =
* bugfix:   SSL support on file includes
* bugfix:   double slash in file includes

= 14.6 =

* added:    NEW: my_cforms_filter()  in my-functions.php  (input filter)
* bugfix:   Security/SPAM issue with TellAFriend feature
* added:    Avoiding wpautop screwing with the layout
* added:    Changed the_content filter priority to deal with themes demanding wpautop
* bugfix:   multi-part forms: RESET button resets complete input and not just session

= 14.5 =

* added:    WP3.4 compliance
* bugfix:   CAPTCHA security issue; can't override captcha settings via URL params
* bugfix:   re-enabled version update info on the native WP plugins page

= 14.2 =

* added:    added more IQ to my-functions.php:
            "adminEmailSUBJ" & "autoConfSUBJ" to change the subject line during run-time
            Also, check out Regis' code example in this new section!
            Thanks Regis!
* added:    "\n" in the default text of textarea fields is recognised as a new line character
* bugfix:   Call-time pass-by-reference in lib_aux.php
* bugfix:   XML download, imports now nicely into excel, even with multi-line fields
* bugfix:   corrected superfluous html5***** settings for non html5 fields
* bugfix:   bigger field label issues when using custom IDs and regexp
* bugfix:   localization issue with lib_editor.php and added French translation for the editor. Thanks Regis!

= 14.1 =

* bugfix:   localization in wizard popups
* bugfix:   array declaration error some experienced in lib_activate.php
* bugfix:   header inclusion/exclusion on specific pages

= 14.0 =

* enhanced: tabbed navigation between form configuration fields
* added:    date picker support for year selection drop down
* added:    HTML5 support, new Global Settings (must be enabled first) and Form Options:
            html5color, html5date, html5datetime, html5datetime-local, html5email, html5month, 
            html5number, html5range, html5search, html5tel, html5time, html5url, html5week
* added:    new my_cforms_logic() routines "fileDestination", "fileDestinationTrackingPage" for uploads
* bugfix:   date picker start day
* bugfix:   loading of calendar style (css)
* bugfix:   form submission restrictions (from/to date and number)
* bugfix:   extra slash removed from plugin URL (fixes path to custom CSS file in /cforms-custom/)

= 13.2.2 =

* bugfix:   CC field issues for MP forms
* bugfix:   Reply-To fixed in admin email
* bugfix:   fixed 'custom names' form feature
* added:    hidden field default value supports cforms {default variables},eg. UserID, Name etc.

= 13.2.1 =

* bugfix:   undefined function 'my_cforms_logic' 

= 13.2 =

* bugfix:   XSS security issue with lib_ajax.php
* bugfix:   several fixes around "CC: me" field
* enhanced: added Help Note on how to use {Custom Variables} in multi-page forms
* bugfix:   default checkbox state is not restored on form reload (user choice remains)
* bugfix:   removed depreciated eregi() & split() calls
* enhanced: better support for SSL and multi-site deployments (WP v3x!)
* bugfix:   "Corrupted Content Error" on FF when backing up settings
* feature:  empty fields can now be excluded from the admin email 
            (Admin Email Message Options)
* bugfix:   multi-page forms: the "disable email attachments" setting for individual  
            forms is now being considered in the final admin email 
* enhanced: fixed several issues with multi page forms (accidental auto resets etc.),
            forms now need to be manually reset via form reset button (setting),
            no auto form reset anymore when on first form!
            form session is properly remembered.
* enhanced: calendar.css adjustments to make styles specific to cforms

= 13.1 =

* bugfix:   radio button fix
 
= 13.0 =

* other:    WP3.3 compliance
* other:    a new more modern default theme/style (cforms2012.css)
* other:    WP 3.3 jQuery / jQueryUI is now being used for the date picker!
* other:    link to supported date formats added to global settings screen
* other:    enhanced dashboard layout / listings
* other:    enhanced in-field editing on tracking page ("view records area")
* bugfix:   checkbox "default state" setting fixed
* bugfix:   checkboxgroup "default states" & "new line" settings fixed 
* bugfix:   several admin screens related fixes
* bugfix:   tracking page, download options fixed (header, URLs etc.)
* other:    checkbox fields now include a "checked symbol", instead of the word "on"
* other:    email layout enhancements (optimized for gmail & HTML capable clients)
            !!! NOTE: in order to fully enable the new layout for old forms, you MUST 
            reset the Admin & Auto Confirmation Messages (see button above each)

= 12.2 =

* other:    pare_str() caused issues for some, commented out now

= 12.1 =

* bugfix:   minor datepicker adjustment (for anyone that uses non standard date display)

= 12.0 =

* other:    WP3.2.1 compliance
* other:    upgraded to jQuery 1.6.2 (datepicker)
* bugfix:   corrected WP comment form issue ($usermessage_text)
* bugfix:   Zip Code RegExp in the Help Seection corrected
* bugfix:   date picker fix for WP3.2

= 11.9 =

* bugfix:   IE9 date picker fix
 
= 11.8 =

* bugfix:   fixing PHP's issue with uksort()
* bugfix:   upload fields in forms could cause hang ups when submitted from iphone/ipad
* other:    enhanced path determination in cforms.js, should help to avoid issues going forward
* other:    session check in cforms.php to better support existing sessions (shopping carts etc)
 
= 11.7.3 =

* bugfix:   stalling of upload forms fixed (or ones with alternative form action)

= 11.7.2 =

* other:    enhanced support for form field arrays, e.g. :  my-field[id:address[]]
* bugfix:   fixed jQuery wizard editor error 
* bugfix:   fixed "upload field" handling & form error (stalling)
* bugfix:   sorting fixed for API call "get_cforms_entries()" - hopefully for good

= 11.7.1 =

* bugfix:   fixed function_exists() call in lib_functions.php
* bugfix:   fixed some date picker related bugs
* bugfix:   fixed PHP call insert_cforms() to accept "Form Names" as input (again)

= 11.7 =

* bugfix:   much enhanced/fixed REGEXP support for multi-line fields, to better support anti
            SPAM measures, e.g. :   ^(?!.*(xxx|seo|ptimization)).*$

= 11.6.1 =

* bugfix:   fixed missing spaces in multi-line text fields

= 11.6 =

* added:    addded Form Name to `<form>` element for easier styling across sites
* other:    changed edit / delete icon for better distinction
* other:    plugged a potential (but unlikely to be exploited) security hole
* other:    changed MySQL calls from direct code to using $wpdb
* other:    updated included PHPMailer code to the latest release
* bugfix:   fixed issue with undefined "file size limit"
* bugfix:   sorting fixed for API call "get_cforms_entries()"
* bugfix:   textual changes

= 11.5 =

* other:    WP 3.0 compliance

= 11.4 =

* feature:  added Belarusian (be_BY) by Marcis G.
* feature:  NEW CORE OPTION : disables enctype, to support e.g. salesforce.com
* feature:  enhanced JS regular expression validation, supporting multi-line searches/
            prevention from using spam words, e.g. ^(?:(?!spam-word|seo).)*$
* feature:  on UPLOAD forms, submit button gets disabled to prevent false resubmission
* bugfix:   fixed double quote "" issue in XML tracking downloads
* bugfix:   fixed loading issues of the TRACKING page
* bugfix:   fixed deletion of ATTACHMENTS on server when deleting tracked submission
* bugfix:   form names for recent submissions show accuratly now on WP DASHBOARD
* enhanced: added security around "remove/delete plugin" function

= 11.3 =

* bugfix:   MULTIPLE RECIPIENTS: fixed issue with 'required' & non-valid entries

= 11.2 =

* bugfix:   WP 2.9 idiotic  $_REQUEST / get_magic_quotes_gpc() override in wp-settings.php

* feature:  cforms headers: can now also be *excluded* on certain pages (Global Settings))
* bugfix:   file upload: fixed renaming of multiple attachments (multiple '-' issue)
* bugfix:   file upload: fixed missing default upload path (absolute path))
* bugfix:   TAF: fixed *TAF show/noshow* for TAF forms in the sidebar and in a post/page
* bugfix:   auto confirmation settings: fixes saving of "subject" text
* bugfix:   drop down / select box: fixes "selection of last item" issue after form reload
* other:    "don't clear on reset" auto. turned off when enabling multi part/page forms
* other:    improved page load and UI experience, fixed some UI text areas

= 11.1 =

* bugfix:   SENDBUTTON: fixed support for quotes and single quotes in submit button text
* bugfix:   get_cforms_entries: fixed old data issue in $cfdata
* bugfix:   MULTI RECIPIENT: fix for non-valid '-' entries
* bugfix:   TELL A FRIEND: added option to turn off CC: for submitting user
* bugfix:   WP COMMENT FORM: major bugfix for ajax & non-ajax submission
* bugfix:   UTF8 tracking page downloads are now properly coded utf8
* bugfix:   Javascript for SAFARI optimized (no more time-outs)
* bugfix:   MULTI-RECIPIENT documentation on Help! page corrected

= 11.0 =

* feature:  MULTIPLE RECIPIENTS support revamped (supporting multiple email accounts per option)!
            PLEASE DOUBLE CHECK YOUR SETUP! see HELP! page as well.
* feature:  admin UI revamped (please note the new fixed 'action box', right hand side!)
* feature:  PHP 6 compliance: added input processing support to accomodate PHP's DEPRECATED
            get_magic_quotes_gpc()
* feature:  added a new my-functions.php logic routine: "successMessage"
* feature:  UPLOAD field noid- (or ID) prefix can be turned off
* feature:  enabled Email-Priority for the SMTP feature
* feature:  admin UI option boxes: supporting clickable option titles bars
* feature:  added two new system variables: {CurUserFirstName} & {CurUserLastName}
* feature:  supporting multiple form fields with the same name in get_cforms_entries()
* feature:  BCC field now supports multiple email addresses
* feature:  conditional redirection (my-functions.php) can now return "" (empty string) to cancel
            redirection altogether

* bugfix:   fixed "magic deletion" of \ (backslashes) for some users when updating settings
* bugfix:   fixed popup time entry for "submission limit"
* bugfix:   form deletion: fixed error when having more than 10 forms.
* bugfix:   fixed deletion of auto confirmation settings when turning off this feature
* bugfix:   fixed form duplication bug that would prevent 'Multi-part settings' to be copied
* bugfix:   form deletion: fixed proper consolidation of remaining forms / settings array
* bugfix:   TAF: fixed WP quickedit; now keeping the TAF setting when updating post
* bugfix:   fixed sorting for int() values in get_cforms_entries()
* bugfix:   "geomaplookup" call adjusted to new URL
* bugfix:   RSS title missed a stripslashes()
* bugfix:   Admin email missed a stripslashes() for the form data title
* bugfix:   Tracking/download of CSV: "Add URL for upload fields" option fixed for MP forms

* other:    plugin removal code enhanced (turns off plugin automatically now)

= 10.6 =

* feature:  completely rewritten sidebar WIDGET SUPPORT, pls recheck your widget setup!
* feature:  auto confirmation messages now support an attachment
* feature:  radio buttons can now be "required" when no radio box should be checked by default!
* feature:  added strip_tags() to TEXT message part of the admin email
            (to avoid HTML in plain TXT)
* other:    house keeping, cleaning up & rearranging admin UI
* feature:  backwards compatible now: supporting PHP4
* bugfix:   label issues with "(" and ")", which would not show in the admin email (&tracking)
* bugfix:   minor code fixed, mostly cosmetic
* bugfix:   nonAjax form submission would not allow single quotes in custom field ID/NAME
* other:    amended Help section
* other:    typos/corrections on Help! page

= 10.5.2 =

* bugfix:   WP comment form feature:: fixed {custom var} replacement in admin email (ajax mode)
* bugfix:   WP comment form feature:: fixed "send to author" option in cforms'
* other:    admin email addresses now support "+" , e.g. john+janey@mail.com

= 10.5.1 =

* feature:  Email Priority can now be set under "Core Options"
* bugfix:   insert_cform('1') would cause minor issues if your default form was a TAF form
* bugfix:   Limit Text is now saved even if no #-limit is provided
* bugfix:   fixed =3D issue for some users (admin email layout)

= 10.5 =

* feature:  redirect options: "hide" & "redirect" have been split, allowing both
            find 'hide' under 'core options' now
            make sure to check both options, they may have been reset!

* feature:  email: supporting mail server that strictly require CRLF
            new option under GlobalSettings > Mail Server Settings

* bugfix:   field labels that would have an escaped character (e.g quotes) would not be
            referenced correctly and required a slash until now (e.g. {Your friend\'s Name} ).
* bugfix:   fixed minor XSS cross site scripting vulnerability
* bugfix:   select boxes: fixes the "auto select" of the last drop down option
* bugfix:   email: fixes CR's in multi-line fields
* bugfix:   email: fixing SMTP / PHPMailer attachments
* bugfix:   email: MIME / boundary fix
* bugfix:   email: several cosmetic fixes
* bugfix:   WP comment: fixed Ajax submission of WP comments
* bugfix:   fixed get_cforms_entries() when using "form name" parameter
* bugfix:   fixed WP comment form 'comment id not found' error

* other:    plugin update notice has become smaller / can be toggled
* other:    updated Help page (API section)
* other:    enhanced API function   get_cforms_entries()
            Parameters:
            get_cforms_entries( $fname,$from,$to,$cfsort,$limit,$cfsortdir )
               $fname     = [text]    : form name (case sensitive!)
               $from      = [date]    : e.g. 2008-09-17 15:00:00
               $to        = [date]    : e.g. 2008-09-17 17:00:00
               $cfsort    = [text]    : any input field label, e.g. 'Your Name'
                                        or 'date', 'ip', 'id', 'form', 'email'
               $limit     = [numeric] : limits number of records retrieved
               $cfsortdir = [text]    : sort direction 'asc' or 'desc'

            (see updated Help page too!)

= 10.4 =

* feature:  new my_cforms_logic() features:
            "adminEmailTXT" & "adminEmailHTML", "autoConfTXT" & "autoConfHTML"
            to support run-time changes in the email message part(s),
            e.g. {user_variable} substitution supporting custom/one-off messages
* feature:  tracking / download: revised and fixed downloads that include field headers
            in several areas
* feature:  tracking / download: added optional inclusion of attachment/file URLs
* feature:  tracking / download: added optional inclusion of IP address in report
* feature:  tracking / download: completely revised the tracking download function to
            accommodate very large numbers of records, make sure that js/include/data.tmp
            is writable!
* bugfix:   fixed access to $subID from within my-functions.php for Ajax post method
* bugfix:   file uploads/attachments: revised, better internal handling
* bugfix:   file uploads/attachments: fixed link/URL issue for MP forms
* bugfix:   file uploads/attachments: all files are now being attached to admin email for
            MP forms
* bugfix:   WP comment form: fixed email to author in non Ajax mode
* bugfix:   Fixed "extra variables" (e.g. {Title}) to display again in admin email etc.
* bugfix:   success message: fixed buggy message display ('*$#..') in special cases
* other:    Javascript Date Picker updated to latest release
* other:    admin emails: turned upper case HTML tags to lower case
* other:    admin emails: swapped LF for CRLF (according to RFC822 Section 3)

= 10.3 =

* feature:  RSS feed: revised and enhanced feeds (all & indivudal form feeds)
            supporting inclusion of form fields in the feed
* feature:  new API function: cf_extra_comment_data( $id )
            will retrieve all extra fields per given comment ID in a data [array]
            see tutorial online (troubleshooting forum)
* feature:  enhanced option to keep input field data (no reset) after submission
            (both ajax & nonajax)
* feature:  cforms allows now to optionally turn off the admin email (and only track via DB)
* feature:  "redirect on success" can now be used to jump to any location on your page after
            form submission
* feature:  threaded comment support for cforms' WP comment feature
            note: nonAjax will work as is, for the Ajax method you need to adjust your WP theme to
            work nicely with cforms (dynamically set the parent container!)
* bugfix:   multi-part forms: no more session resets / jumping to first form
* bugfix:   multi-part forms: confusion of prev. entered field values when going to previous form
* bugfix:   escaped '<' & '>' in input fields, which allows to properly send code in a form as well
* bugfix:   a few admin CSS fixed (e.g. resizable text areas)
* other:    smaller cosmetic fixes
* other:    input field default values will now remain actual values and not be cleared!

= 10.2 =

* bugfix:   WP comment form : fixed issues when form #1 was enabled as WP comment form replacement
* bugfix:   fixed Cross-site_scripting security hole (rarely to be exploited but anyway)
* bugfix:   multi-part form: fixed issue, when first form is the default form (#1)
* bugfix:   multi-part form: fixed escaped quotes when going back
* bugfix:   textarea fields: fixed carriage returns in HTML part of admin email
* other:    buttonsnap has been removed
