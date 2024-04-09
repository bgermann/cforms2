=== cformsII ===
Contributors: bgermann, cbacchini, codifex, nb000, wdfee, passoniate
Donate link: https://www.betterplace.org/projects/11633/donations/new
Tags: contact form, contact, form, post, sidebar, multi step, api exposed, fork
Requires at least: 5.2
Tested up to: 6.5
Stable tag: trunk
License: GPLv3 or later
License URI: http://www.gnu.org/licenses/gpl-3.0

== Description ==

This is a fork of cformsII, a highly customizable, flexible and powerful form builder plugin, covering a variety of use cases and features.

Oliver, the original author, discontinued developing the plugin. This fork is an effort to keep it up to date.

If you want to use plugin versions older than 14.6.3, you should rename the directory containing the plugin from "cforms2" to "cforms". But bear in mind that old versions should not be used in public systems, because they contain [known serious vulnerabilities](https://wpvulndb.com/plugins/cforms) that are exploited in the wild.

The [current security baseline version](https://wpvulndb.com/plugins/cforms2) is 15.0.7.


== Related Plugins ==

[Really Simple CAPTCHA for cformsII](https://wordpress.org/plugins/cforms2-really-simple-captcha) provides an image CAPTCHA via cformsII's pluggable CAPTCHA facility. To convert the old CAPTCHA fields to Really Simple CAPTCHA, please install the current version and activate the plugin.

[Contact Form DB](https://cfdbplugin.com) has support for storing and exporting cformsII's submission data. Be sure to enable CFDB's capture submission support for cformsII.

[Old Tracking DB for cformsII](https://wordpress.org/plugins/cforms2-old-tracking-db) is for those who do not want to switch from the built-in Tracking DB to CFDB. However it does not have any web interface.

[ReCaptcha Integration for WordPress](https://wordpress.org/plugins/wp-recaptcha-integration) provides a reCAPTCHA implementation for cformsII.


== Localization ==

You can see the available languages and contribute via [GlotPress](https://translate.wordpress.org/projects/wp-plugins/cforms2). Only some of them that are almost completely translated are installed automatically.

If you want to install another language, please choose its cell in the Development column, export it as Machine Object Message Catalog (.mo) and put it in the wp-content/languages/plugins directory.


== Roadmap ==

Some things are to be done:

* add a Gutenberg equivalent of the editor plugin
* filter user input better
* rework multi-part forms to be independent from PHP sessions
* refactoring with object oriented approach
* long term: unit tests and continuous integration


== Installation ==

= Installing the plugin =

You can install the plugin via WordPress Dashboard. It should show up by searching for cforms2. If this does not work for you, there should be an option to upload a zip file, which is available on the [wordpress.org plugin directory](https://wordpress.org/plugins/cforms2/).

If you want to install manually, please upload the complete plugin folder "cforms2", contained in the zip file, to your WordPress plugin directory!

If you want to check integrity of the download, please use the cforms2.*.sig GPG signature files that are published via [GitHub releases](https://github.com/bgermann/cforms2/releases). The [key used for signing](https://keys.openpgp.org/vks/v1/by-fingerprint/D9426F9637DCA799FF0F9AF22626D16964438E53) has the fingerprint `D942 6F96 37DC A799 FF0F  9AF2 2626 D169 6443 8E53`. The git tags themselves are also signed beginning with version 14.8.

= Upgrading the plugin =

If you want to upgrade from the original cformsII from deliciousdays.com, please upgrade to [version 14.6.0.5](https://plugins.svn.wordpress.org/cforms2/assets/cforms2.14.6.0.5.zip) first, make sure you upgraded your settings (resave your global settings and every form) and [backup your database](https://codex.wordpress.org/Backing_Up_Your_Database). Then deactivate the original plugin. If you want to migrate your tracking database to CFDB, please install version 14.14 and migrate it. Then you can install the current version of this fork.

The form inclusion does not work with HTML comments (`<!--cforms name="..."-->`) anymore. You have to use shortcodes [cforms name="..."] now. Please change the form calls accordingly.

If you want to delete the 14.6.0.5 version by deleting directory "cforms" and you have any cforms-uploaded files, make sure you do not delete your attachments directory, which is contained in the cforms directory by default.

= Custom theme (CSS) files =

Create a folder in your WordPress plugins directory and call it "cforms-custom". Move all your custom theme files into it. This folder will protect your changes going forward.

Check out the [cforms CSS Guide](https://web.archive.org/web/20220516192303/https://www.deliciousdays.com/download/cforms-css-guide.pdf) on layout customization.


== Frequently Asked Questions ==

= Why do I not get any emails from my form? =

Most probably this is not cformsII's fault. Please check your WordPress mail configuration with a plugin like [Check Email](https://wordpress.org/plugins/check-email/). If that reports not to work, you don't have a mail server running or its settings are broken. Please fix it yourself, tell your hosting provider or use an external SMTP plugin (see the next question).

= Where are the external SMTP settings? =

That function was removed. The WordPress function [wp_mail](https://codex.wordpress.org/Function_Reference/wp_mail) is used for mails now, which makes use of built-in PHPMailer by default. If you want to configure it to use an external SMTP server, use an appropriate plugin, e.g. [WP Mail SMTP](https://wordpress.org/plugins/wp-mail-smtp/).

If wp_mail is replaced by some plugin, multipart/alternative emails may not be sent properly, which is the case with e.g. Postman SMTP Mailer/Email Log.

= Why is the Form Settings page broken? =

This is an incompatibility with WordPress 4.2+ that was fixed with cformsII 14.9.3. Be sure to use a current version.

= How can I contribute code? =

Please use [GitHub pull requests](https://github.com/bgermann/cforms2/pulls).

= Where are comment and Tell-a-Friend fields? =

These features were removed with version 14.12.

= Where is my database view? =

This feature was removed with version 15.0.

= Does cformsII expose an API? =

cformsII can be extended via WordPress actions and filters. You find their 'cforms2_' prefixed names and inline documentation at the apply_filters and do_action calls. Additionally there is one API function that you can call directly: insert_cform. Please have a look at its PHPDoc. Older cformsII versions had an API called my-functions, but that is removed as of version 14.14.


== Upgrade Notice ==

= 15.0.7 =
The tracking database feature and its view are removed with cformsII 15.0+. Your data still exists in the database. Please have a look at CFDB plugin as a replacement.


== Changelog ==

= 15.0.7 =

* bugfix:   prevent XSS by escaping output: CVE-2023-52203 and CVE-2024-22149

= 15.0.6 =

* other:    (unpublished)

= 15.0.5 =

* bugfix:   Fix CSRF by introducing nonces to admin forms (CVE-2023-25449)

= 15.0.4 =

* enhanced: make session extension optional
* other:    remove cookie-based content hiding
* other:    remove custom email line ending

= 15.0.3 =

* bugfix:   validate q&a field hint id

= 15.0.2 =

* bugfix:   validate {IP} being an IP address, preventing CSRF or other similar attacks
* other:    remove {Referer} substitution variable

= 15.0.1 =

* enhanced: remove deprecated html5datetime field type
* bugfix:   write html5 attributes to field name (long-standing since 14.12.2)

= 15.0 =

* enhanced: use HTML5 date field on Form Settings page for the start and end dates
* enhanced: move style settings to Global Settings
* bugfix:   do not behave differently for 0 as for any other default value
* bugfix:   compatibility with PCRE2 evaluating regex character classes differently
* other:    remove tracking database and its view
* other:    remove track_cforms capability
* other:    remove confirmation email attachment
* other:    remove JavaScript date picker
* other:    remove all deprecated features and warnings about their usage
* other:    remove debugging cruft

= 14.14 =

* enhanced: deprecate insert_custom_cform in favour of insert_cform
* enhanced: for consistency, run JavaScript also for non-AJAX forms
* enhanced: enable more than one CC me email addresses
* enhanced: remove extra email and tracking elimination setting for multipart forms
* bugfix:   do not mark all fields invalid if just one field is invalid for non-AJAX forms
* bugfix:   reset multipart forms on every first form
* bugfix:   checkboxes do not use right label if defined
* added:    feature to copy old submission data to CFDB
* added:    WordPress filters cforms2_admin_email_filter, cforms2_cc_me_email_filter, cforms2_auto_conf_email_filter, cforms2_usermessage_filter
* other:    remove persistent file storage (please use CFDB)
* other:    remove 3rd party read-notification support
* other:    remove submission limit feature
* other:    remove Global Settings for Tracking DB
* other:    remove Tracking ID for files and {ID} variable
* other:    remove deprecated HTML comment placeholder
* other:    remove deprecated API functions my_cforms_logic, my_cforms_filter and my_cforms_ajax_filter
* other:    remove image CAPTCHA settings

= 14.13.3 =

* bugfix:   fix some verified and some possible authenticated XSS vulnerabilities
* bugfix:   only redirect non-ajax forms for validated forms
* bugfix:   wrong condition for error on auto confirmation message

= 14.13.2 =

* bugfix:   fix fatal error caused by using empty() on a function return with PHP < 5.5

= 14.13.1 =

* bugfix:   wrong parentheses

= 14.13 =

* bugfix:   fix authenticated SQL injections in the tracking DB GUI by removing "Delete Entries" and "Download Entries" features and filtering user input
* bugfix:   do not attach the upload directory to auto confirmation mails
* added:    hook wp_mail_failed action to give warnings with PHPMailer error details
* added:    cforms2FormSent JavaScript event on successful form submission
* other:    remove restore feature and with it jqModal dependency
* other:    remove tracking DB GUI's search
* other:    remove Dashboard widget
* other:    remove the remaining translation files
* other:    remove the form presets
* other:    remove wide_brown_light theme
* other:    remove deprecated my_cforms_logic callers: redirection, successMessage, ReplyTo, adminTO, adminEmailSUBJ, autoConfSUBJ, fileDestination and fileDestinationTrackingPage

= 14.12.3 =

* bugfix:   repair wrong cforms2_fieldtype_multi_id construction

= 14.12.2 =
* bugfix:   reorganize session reset
* enhanced: replace jqModal with jquery-ui-dialog in some dialogs
* other:    remove fancy-dark theme

= 14.12.1 =
* bugfix:   use right number of fields on dynamic forms
* bugfix:   really change my_cforms_ajax_filter() API function's data structure to the same as my_cforms_filter (as logged for 14.11)
* enhanced: always set fancy error messages, label IDs and element IDs
* enhanced: do not force MyISAM as database engine for the cformsII tables
* enhanced: use default character set for the cformsII tables to enable utf8mb4 if available
* enhanced: make debug output editable
* other:    remove "Jump to Error"
* other:    remove backup in weird NUL offset format

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
* bugfix:   form names for recent submissions show accurately now on WP DASHBOARD
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

* feature:  RSS feed: revised and enhanced feeds (all & individual form feeds)
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

= 10.1 =

* feature:  added form option to turn off tracking for a given form only
* bugfix:   improved method of adding admin scripts/CSS (to better support preWP2.7 systems)
* bugfix:   added UI CSS mods to accomodate pre WP2.7 admin interface
* bugfix:   wrong dashboard icon extension (png vs gif)
* bugfix:   hover text over AJAX option on 'form settings'
* bugfix:   multi-page forms: after final submission, form #1 on occasion would render only partly
* bugfix:   multi-page forms: if used with "WP comment form feature" at the same time, comment form would also be replaced with the current multi-part form
* bugfix:   multi-page forms: when deploying several MP form series the SESSION would in some cases not be reset
* bugfix:   in v10.0 access priviliges for tracking required to be "manage_cforms"
* other:    fixed CommentLuv support
* other:    WP2.7+ fixed admin UI : support for removeable top err/warning messages
* other:    a few minor UI adjustments to accommodate 1024px wide screens a tad better
* other:    some cosmetic icon adjustments

= 10.0 =

* feature:  "manual/help page" now also offered in form of a PDF (see help page)
* feature:  enhanced Opera Browser support
* feature:  complete admin UI update: WP2.7'ized it (code and CSS)
* feature:  multi-part/-page form support
* feature:  added dashboard support for WP 2.7
* feature:  general enhancements on tracking page (time stamps on entries etc)
* bugfix:   regexp now allow OR | operator,e .g. ^(a|b)c
* bugfix:   datepicker localization for admin interface
* bugfix:   email verification fields (special regexp) would not work with custom err messages
* bugfix:   fixed install bug that appeared on some WP 2.7 deployments (redeclare err)
* bugfix:   fixed captcha issue for nonAjax forms
* bugfix:   insert_cform() would show the default form versus a specified one
* bugfix:   minor admin email issue re: inclusion of CAPTCHA input
* other:    updated and cleaned up help page
* other:    much improved admin captcha config preview (no saving necessary for preview anymore!)
* other:    made some admin UI changes (font alternatives)
* other:    enhanced handling of duplicate form fields for tracking/admin email (no more __x suffixes)
* other:    revamped admin email assembly process

= 9.4 =

* feature:  specific insert position can now be determined when adding new fields
* feature:  TAF: added support for proper WP 2.7 custom field registration (screen options)
* bugfix:   fixed some WP2.7 admin UI incompatibilities
* bugfix:   fixed TAF-option for WP2.7 New post/page UI (drag&drop, open/close)
* bugfix:   multi-select / check box groups for multi-page form hack
* bugfix:   special characters would cause captcha reload to revert back to certain default settings
* bugfix:   fixed autosave issue with TellAFriend flag being deleted
* other:    added default number (5) of shown RSS entries, if not configured by user

= 9.3 =

* feature:  added title feature to widget control
* other:    added explanatory code to API call "to retrieve all tracking records"
* other:    corrected some typos
* other:    label and input fields now support capital letters
* bugfix:   removed debug message in API call to retrieve all tracking records
* bugfix:   v9.2 required plugin to be activated 2x for it to show up in the admin menu
* bugfix:   for IIS servers abspath.php needs to escape the backslash

= 9.2 =

* feature:  added restore function: when cforms settings get corrupted (for whatever reason) the function kicks in as soon as cforms detects broken settings and will guide the user
* feature:  added form specific success and failure classes for more CSS/styling control
* feature:  added POP before SMTP authentication to SMTP feature (global settings)
* feature:  added filename modification for file uploads at run-time (see my-functions.php!)
* bugfix:   fixed a minor bug in the global settings backup routine
* bugfix:   "don't clear" setting would not be saved
* bugfix:   fixed disabling of headers when downloading tracked data
* bugfix:   tracking data download: missed last record for CSV & TAB
* bugfix:   fixed and simplified 'duplicate form' routine (FROM: was not copied properly)
* other:    TinyMCE styling : made CSS more specific to cforms ("TinyMCE Advanced" compatible)
* other:    update SMTP/PHPMailer support to v2.2.1

= 9.1 =

* feature:  timing of forms: added start and end date for forms availability
* feature:  optionally include field names/header info when downloading data sets (CSV, TAB, XML)
* feature:  new custom function to control application logic during run-time: my_cforms_logic() filter function, see my-functions.php for now only page redirection is being supported, more to come in the future
* feature:  revamped the code driving the deletion of forms (allowing also allowing the first form to be deleted)
* bugfix:   PHPMailer support for attachments (couldn't attach files to email)
* bugfix:   "Cannot use string offset as an array in lib_aux.php on line 12"
* bugfix:   minor bug in rendering nonAjax action path in form tag
* bugfix:   fixed paging on the tracking page
* bugfix:   fixed issue with cforms WP comment feature and additional fields (on top of the default comment fields) not showing up in notification email
* bugfix:   fixed CC:me field bug (for Ajax submission)
* bugfix:   form submission tracking : fixed dashboard & RSS link to actual form details
* bugfix:   fixed RSS support (mime types and general compatibility issues)
* bugfix:   JS bug could have caused hiccups when refreshing CAPTCHA image
* bugfix:   fixed restore bug "Error: Specified file failed upload test."
* other:    added delete warning when deleting a form
* other:    line breaks (CRs) in nonAjax confirmation msg are now translated to <br/>

= 9.0b =

* bugfix:   a couple of custom variables were broken, e.g. {Form Name}
* bugfix:   fixed lib_WPcomment path issues
* bugfix:   fixed include path in lib_ajax.php
* bugfix:   now migrates successfully forms 10-... from pre v9 versions
* other:    added warning message in case abspath.php could not be created
* other:    made some missing text translatable

= 9.0 =

* feature:  API function: get_cforms_entries( $fname, $from, $to, $sort, $limit ) (see HELP for further info: "APIs...")
* feature:  optionally redisplay user input after form submission (non Ajax mode only)
* feature:  complete cforms backup option (see under "global settings")
* feature:  enhanced support for WP_CONTENT_DIR, WP_CONTENT_URL, WP_PLUGIN_DIR, WP_PLUGIN_URL, PLUGINDIR
* feature:  supports now 'check-box-activation' in "plugins" (no more "all data erased error")
* bugfix:   installing the preset would add unwanted carriage returns (would break cforms Backup feature)
* bugfix:   fixed widget/sidebar form display (if more than the default form was displayed)
* bugfix:   fixed checkbox 'false' text addition if set to be not selected
* bugfix:   form name filter on tracking page would only return results for one form
* bugfix:   several minor bugfixes re: tracking page (filter + view, delete & download)
* other:    my-functions.php can now be moved to the /cforms-custom/ folder
* other:    data tracking page: made trash can icon(s) more obvious, added close button
* other:    upload field: file extensions are case insensitive now!
* other:    upload field: spaces in file names are now converted to _
* other:    fixed a Javascript related security concern
* other:    revamped form backup file (not compatible with pre v9.0 versions!)
* other:    fixed GMAIL support for admin message layout
* other:    tracking page layout more fluid (CSS: Show->Submissions)

= 8.7 =

* feature:  added support for other plugins using TinyMCE (causing the 'purl' err currently). See buttons support, global settings.
* feature:  RSS feeds (security key enabled) for form submissions, global feed and for single forms. See global settings and main form admin page.
* feature:  option to force CAPTCHA & Q&A even for logged in users. See global settings.
* feature:  CAPTCHA now compatible with WP Super Cache plugin working with super cached pages!
* feature:  cforms "comment feature" now considers comment COOKIES (user preferences)
* bugfix:   fixed email (data) issue when CAPTCHA or Visitor Verification (Q&A) field was not at then end of the form
* bugfix:   non-(at all)-selected radio buttons would cause issues
* other:    rearrangement of options on the main config page & admin UI clean up

= 8.6.2 =

* feature:  Q&A now also not displayed for logged in users
* bugfix:   email bug: broken HTML message when user was logged in while submitting a form
* bugfix:   comment feature, ajax submission: 'wait a moment...' wasn't replaced by success message

= 8.6.1 =

* feature:  'Comment Luv' fully supported by cforms' WP comment form feature
* feature:  'Subscribe To Comment' plugin fully supported by cforms' WP comment form feature
* feature:  'WP Ajax Edit Comments' plugin fully supported by cforms' WP comment form feature
* feature:  CAPTCHA not shown when users are logged in
* feature:  WP comment form feature supports gravatars (see HTML template for new comments -> HELP)
* bugfix:   turning WP's "wp_autop" filter off, could cause form rendering issues
* bugfix:   fixed jumpin check box when check box is 'required' and toggled
* bugfix:   when CAPTCHA was set to case insensitive, reloading proper captcha was iffy in some cases
* other:    supports WP2.6 "wp-load" / alternative wp-config.php option
* other:    added "wp-load" support to buttonsnap class
* other:    enhanced rendering of cforms comments (for WP post/page comments). See new comment template on global settings for new options and best pratice setup
* other:    some general house keeping

= 8.6 =

* feature:  added user definable default states for check-, radio boxes, drop downs etc.
* bugfix:   fixed proper cforms tag insertion when using PHP4 (known PHP strrpos issue)
* bugfix:   fixed funny "!" characters in long emails (when lines had gotten too long)
* bugfix:   fixed better handling of very large upload files (when "no email attachments" are selected)
* other:    added 'echo' to insert_custom_cform()
* other:    minor adjustments to the file upload handling routine
* other:    changed to use of $_SERVER['REQUEST_URI'] to accommodate IIS server peculiarities

= 8.5.2 =

* bugfix:   line breaks in check box groups could cause field troubles (overwriting)
* feature:  allowing other forms to "feed" into a (c)form and thus pre-populate cforms fields
* bugfix:   fixed "custom err messages" when used with "custom IDs/Names"
* bugfix:   fixed plugin_dir path logic to better support WPMU
* bugfix:   tracking page: fixed getting all entries
* other:    insert_cform() now prints directly the form (no echo needed)

= 8.5.1 =

* feature:  user CAPTCHA response can now be treated case insensitive
* bugfix:   tracking page: "download all" fixed
* bugfix:   tracking page: filtered results count fixed
* bugfix:   fixed some annoying MSXML IE specific errors...
* bugfix:   custom files had to be all lowercase, now case insensitive
* bugfix:   custom CSS file would not be pre-selected in Styling/drop-down
* other:    added a patch to manage WordPress annoying wp_autop 'feature' and thus fix XHTML validation (this should really be WP's task ;-)

= 8.5 =

* feature:  better custom-files support (CSS, CAPTCHA) to outwit the short comings of the WP auto update feature that removes/overwrites custom files *ALL custom files** should go into "/plugins/cforms-custom"
* feature:  added/changed default way of referencing forms, now: <!--cforms name="XYZ"--> for better transparency and persistence (when deleting forms)
* feature:  WP comment feature:  extra admin notification option available now
* bugfix:   radio option ID != label "for=", now they match up
* bugfix:   fixed 'waiting' message while submitting (escaped characters & styling)
* bugfix:   fixed RoleManager support for the new & enhanced tracking page
* bugfix:   fixed HTML tags in checkbox group display text (wizard dialog)
* bugfix:   fixed HTML tags in checkbox group display text (in form email)
* other:    enhanced sanitizing of custom IDs for input fields
* other:    added {Referer} & {PostID}
* other:    enhanced XML download format
* other:    replaced all deprecated get_settings()
* feature:  added the possibility to change the FROM: address to fake the user's
            this is not recommended, but a widely asked for 'feature', use at your own risk

= 8.4.2 =

* bugfix:   date picker field would be greyed out even if enabled in global settings
* bugfix:   check box group label ID <> input field ID, broke xHTML strict
* bugfix:   fixed support for special characters on the "Tracking" page (Viewing)
* bugfix:   fixed support for special characters on the "Tracking" page (Downloading)

= 8.4.1 =

* bugfix:   some users experienced lost TAF setting when post was scheduled for a future date
* bugfix:   forward slashes are not allowed in form names, and could have caused some issues
* bugfix:   cforms WP comment feature : "Select:Email/Note" wizard dialog corrected
* bugfix:   revived suppressed err msg when selecting more than one field of a unique field type
* bugfix:   field type drop down entries 'misaligned' with wizard dialog(s)
* other:    js/lang/en.js - renamed the lang file to the seemingly more common locale setting
* other:    CC:me can (now post-form-submission) be suppressed

= 8.4 =

* feature:  success message is now being parsed for {default} and {custom} variables
* feature:  custom variables (referencing input field) can now be written as {_fieldXY} with XY being the input field no. as it appears in the admin form configuration, e.g {_field2}  =  2nd form field or even as {ID}  where id = [id:ID] when using custom IDS for your input fields
* feature:  enhanced custom input field names: if "[id:]" present in field name string, e.g. Your Name[id:fullname]|Your Name, then what's given as the 'id' is being used for the fields id/name
* other:    changed focus to first missing/invalid input field, used to be the last field in the form
* bugfix:   checkboxgroup ID bug resolved (thanks Stephen!)
* other:    included a fix for other plugins that unnecessarily impose "prototpye" on all plugin pages

= 8.3 =

* feature:  Completely revised Tracking/Edit UI
* feature:  Tracking: XML download
* feature:  Tracking: Editable fields
* bugfix:   fixed IIS issues with CAPTCHA RESET
* bugfix:   datepicker default values (non-digit) would cause false start dates
* bugfix:   "page" wasn't properly recorded in some cases for ajax submission
* bugfix:   multiple upload fields: if the first field wasn't populated, none of the following attachments would be send in the email (but saved on the server)
* bugfix:   if all submissions were deleted from tracking tables, the first new form submission would be partially broken

= 8.2 =

* feature:  new, more robust datepicker feature!
* bugfix:   fixed T-A-F custom field display for post/pages for WP2.5
* bugfix:   T-A-F custom field would not show for pages
* bugfix:   fixed a rare but critical bug when using checkboxgroups|radiobuttons & datepicker
* bugfix:   fixed escaped quotes in Fieldsets names in emails (text part)
* bugfix:   one click updater was mistakenly disabled by "plugin name"
* other:    fixed delete button on tracking (item) page
* other:    if field is set to "auto clear", it will be also now cleared before
            submission if the default value is still present

= 8.1 =

* feature:  additional form presets
* feature:  |title:  now available to add 'titles' to input fields
* feature:  auto guessing (during activation) of proper Ajax path settings (cforms.js)
* feature:  drop down box- and radiob button options can now be moved around in the "edit dialog"
* bugfix:   fixed issue textarea being erased when custom error triggered and shown
* bugfix:   fixed/improved Javascript 'jump to error' feature
* bugfix:   fixed issue with 'hanging' input field editor (wizard dialog)
* bugfix:   fixed issue with field-2-field validation
* bugfix:   fixed broken edit field dialog, 'wizard'(IE6, possibly IE7)
* bugfix:   {Page} var corrected for Ajax
* bugfix:   properly localized months in confirmation emails
* other:    slimmer main cforms file, hopefully helping narrow down PHP MEM issues
* other:    major code clean-up
* other:    improved and streamlined 'Install Form Preset' Dialog
* other:    admin UI WP2.5'yfied

= 8.02 =

* feature:  NEW PLUGIN ROOT FOLDER STRUCTURE to support one-click/auto plugin updates
* feature:  next to supporting $post custom fields, HIDDEN fields can no be fed via URL parameters, e.g.: URL?myVAR=test-string   | & the hidden field set to "myhiddenfield|<myVAR>"
* bugfix:   critical fix for JS based input validation (regexp broken in v7.53!)
* bugfix:   checkbox would not be validated if no custom value was provided
* bugfix:   several issues with "WP comment feature" and sending a note to the post author
* bugfix:   Better SMTP integration / support for other SMTP Plugins,eg. "WP Mail SMTP"
* bugfix:   Fixed compliance with other 'greedy' buttonsnap-using-plugins
* bugfix:   Fixed quotes " in input field values (default value)

= 8.0 =

* other:    WP 2.5 compatibility

= 7.53 =

* bugfix:   SMTP support for username/pw was only workign in conjunction with SSL/TSL
* bugfix:   SMTP did not properly resolve multiple "TO:" admin addresses

= 7.52 =

* bugfix:   fixed widget support (xHTML compliant again)
* bugfix:   WP comment PHP session mgmt fixed (CAPTCHA issue)
* bugfix:   dialog stalls: admin UI error (dialog) when only label is given for a single input field
* bugfix:   improved session mgmt for WP comments feature

= 7.51 =

* bugfix:   WP comment feature:  "comment in moderation" wasn't displayed properly
* bugfix:   CAPTCHA reload didn't appreciate custom settings
* bugfix:   fixed "values" for checkboxes (Help! has been updated, too). If no value provided, 'X' is being used to indicate a checked box. If a value is given, then that value is being used in the admin email.
* other:    added <Line Break> capability to radio boxes!
* other:    REGEXP Validation: if present, validation *WILL* happen regardless of 'is required setting'

= 7.5 =

* feature:  WP comments feature completely revised: no more dependency on wp-comments-post.php + fully supporting comment form validation (esp. nonAjax!) + Ajax'iefied
* bugfix:   PHP regexp testing for '0' caused a false positive
* bugfix:   T-A-F enable new posts/pages by default -> was broken if TAF form was your default (1st) form
* bugfix:   a few CSS fixes (.mailerr and other)
* other:    major admin UI clean-up, making it xHTML compliant again

= 7.4 =

* feature:  CHANGED and improved "custom processing" (see /my-functions.php)
            function my_cforms_action : gets triggered after user input validation and processing
            function my_cforms_filter : after validation, before processing (nonAJAX)
            function my_cforms_ajax_filter : after validation, before processing (AJAX)
* feature:  new system variables referencing the currently logged in user (see Help)
* bugfix:   WP comment feature wasn't fully working for logged-in users
* bugfix:   several, related to multi-selectbox (quotes in values, 'required' flag etc., ajax submission broken)
* bugfix:   new option 'Extra variables' was reset under certain circumstances
* bugfix:   general formatting issue with escaped input characters, e.g. ", \ etc.  - no one noticed??
* bugfix:   email validity check now accepts the + character
* bugfix:   removed bogus >>  echo"***WPCOMMENT";
* other:    removed <br/> from radio boxes, now supporting inline radio-boxes

= 7.3 =

* feature:  added {Author} default variable
* feature:  added IP lookup (GeoMapLookup) to Tracking Table
* feature:  calendar shows "Year" navigation
* feature:  enable cforms only for specified pages, keeps your blog neat
* bugfix:   anyone using "WP Cache enabled" *may* be affected by malefunctioning form deletion/duplication
* bugfix:   line breaks in multi-line text fields are now displayed correctly in the (HTML) admin email
* bugfix:   captcha reset link was corrupted
* other:    CAPTCHA now case sensitive (supporting UPPER and lower case)!
* other:    enabled structuring of drop down "select lists" with multiple `&nbsp;`

= 7.2 =

* feature:  system {variables} can now be used for the "default values"
* feature:  "jump to error" (javascript) can now be turned off
* feature:  added option to turn off CSS (styling) completely
* feature:  added support for full CAPTCHA customization
* bugfix:   fixed hidden field support for Ajax
* bugfix:   added CharSet=utf-8 to SMTP mailer support
* bugfix:   fixed support for complex field labels,e.g. "Your Name<span class="req">*</span>"
* bugfix:   frozen widget panel corrected
* bugfix:   minor XHTML tweak to fix STRICT compliance
* bugfix:   corrected extra <li> when using cform's "WP COMMENT FEATURE"
* other:    modified handling of unset/unknown {variables}, in v7x unset variables would be printed as such
* other:    adjusted tracked time of submissions to reflect blog settings (offset)
* other:    Show Message "below form" can be activated in "form HIDE mode"
* other:    removed rawurldecode() for hidden fields

= 7.11 =

* bugfix:   some server / browser combos caused the site to stall

= 7.1 =

* feature:  4 additional themes (monospace light&dark, fancy white/blue)
* bugfix:   WP comment feature - broken in v7.0
* bugfix:   WP comment feature - success message now being displayed when sending email
* bugfix:   WP comment feature - comment label points to right input field
* bugfix:   fixed and enhanced dynamic form feature
* bugfix:   fixed upload path config error (upload was still working though)
* bugfix:   fixed possible issues with existing WP jQuery (1.1.4) library
* other:    more CSS theme enhancements
* other:    WP 2.3.2 certified
* other:    Turkish language pack available

= 7.0 =

* feature:  much enhanced error display (optional) with direct links to erroneous entries, updated theme CSS (new styles) & embedded custom error messages!
* feature:  "hidden fields" now supported, optionally referencing custom field data via variables (e.g. {meta_data} )!
* feature:  added alternative NAMES and ID's for all INPUT FIELDS derived from their field label (default is e.g.: cf_field_12)
* feature:  added optional support for T-A-F specific {VARIABLES} in non-TAF forms/contexts
* bugfix:   fixed an old non-AJAX post-id bug (caused issues with variables {Title} )
* bugfix:   table start and textarea data had extra <br/> in the form email
* bugfix:   form backup/restore routine would miss a field
* bugfix:   fixed a multi-select-box resizing bug after form submission (Opera)
* bugfix:   fixes checkbox group / next item issue (would prevent the next item to be included in the email)
* other:    major CSS theme revamping (checked for IE,FF & Opera compat.)
* other:    quite a bit of cleaning up
* other:    adjusted CSS for radio buttons to meet checkbox groups'
* other:    changed default FROM: address to what has been configured in WP's settings

= 6.5 =

* feature:  two additional CSS Themes: dark_XL & grey_blocks
* feature:  input field Wizard Mode, hopefully supporting easier field configuration
* feature:  enhanced "Add new field" functionality, supporting multiple new fields with one click
* feature:  added "read-only" ("disabled" would not allow to extract the field value)
* feature:  added an upload dir URL option, in case the upload directory is outside of ../wp-content/..
* bugfix:   if multiple upload fields are present and only some are being used, attachments may not be copied to the local server dir
* bugfix:   if submission entry (w/ multiple attachments) was deleted (via 'Tracking' page) only the first attachment was physically removed from the server
* bugfix:   dashboard would show cforms DB error if tracking tables were N/A
* bugfix:   fixed a MySQL error that would occur when using a LIMIT & tracking turned off
* bugfix:   fixed multiple upload field ID bug, all fields would have the same Element ID
* other:    enhanced widget layout / UI
* other:    T-A-F preset now explicitly turns on auto confirmation
* enhanced: admin UI (input field attributes & CSS fixes esp. for IE6/7)

= 6.41 =

* bugfix:   fixed form submission hide-bug
* feature:  introducing a limit of accepted form submissions, handy for registrations!
* feature:  PHPmailer 2.0 supporting SSL & TLS connections to external SMTP servers (e.g smtp.gmail.com)
* bugfix:   quotes and single quotes when using regexp / single line input fields
* bugfix:   if field is "EMAIL" AND "REQUIRED" it would loose the "REQUIRED" status after first validation attempt.
* bugfix:   semi-critical bug related to the use of CAPTCHA & NON-AJAX submission method
* bugfix:   fixed a label bug that would prevent the form from validating (W3C), introduced in v6.3
* bugfix:   fixed Drop-Down/Select-Box, now displaying the first entry as the default: necessary when checking 'REQUIRED'
* other:    unknown variables {xyz} would be removed, they're now left intact, supporting the use of: <style> p{blabla} </style> in your HTML notification messages!
* other:    enhanced the default styling for the admin email ('included form data block')
* other:    a few form CSS enhancements
* other:    admin user interface enhancements (resizable message boxes)

= 6.3 =

* feature:  slightly enhanced the "redirection options" - adding "hide form"
* feature:  added new input field: masked password field
* feature:  added setWeekStartDay for "date picker"
* bugfix:   fixed IIS support for TinyMCE dialog (some IIS caused path issues)
* other:    slight CSS changes/enhancements (backward compatible)
* other:    Japanese localization now available

= 6.2 =

* feature:  added detection for typical errors
* feature:  added fieldset information in tracking display to maintain context of input fields
* bugfix:   fixed the T-A-F preset
* bugfix:   fixed TinyMCE compatibility issues with WP2.0.2
* bugfix:   fixed wp_get_current_user() issue with WP2.0.2
* bugfix:   fixed tracking issue and {variable} bug when using fields with the same field label now
* other:    reimplemented 'dashboard" support using activity box hook (no more JS)
* other:    changed behavior of an email field. The "Email" flag doesn't anymore imply "Required"
* other:    Danish translation available!
* other:    Russian translation available!

= 6.1 =

* other:    more forgiving to IIS servers with 'a very special set of ENV variables' fixing a potential menu 'display bug'
* other:    combined WPMU and normal WP admin JS / to also cater to 'normal' WP installs specific prototype/jQuery usage
* other:    made cforms.js editbale to cater to specific IIS reqs (URI=...)
* bugfix:   form name would escape single quotes
* bugfix:   proper support for blogs with a URI prefix, e.g. /blog/wp-content/...
* bugfix:   added user rights check for dashboard display

= 6.0 =

* added:    TinyMCE: enhanced visual appearance of form placeholder in TinyMCE editor
* added:    TinyMCE: much improved TinyMCE & std editor button/insert dialog (now with direct form select & fully localize-able )
* added:    completely revamped admin UI JS core for drag&drop (fixing some IE issues)
* added:    preset forms for "quick starts" (basic, T-A-F, WP Comment and custom err)
* added:    if no 'required' text is given (empty), then HTML will be entirely omitted
* bugfix:   CAPTCHA: finally fixed PHP/GD bug where image would not be shown
* bugfix:   Date Picker/CSS : fixed nasty IE 6 'select box' bug
* bugfix:   Date Picker/CSS : had a fix background color
* bugfix:   Date Picker/CSS : wasn't working with some themes
* bugfix:   Date Picker : wrong default setting for "date format"
* bugfix:   fixed {Page} Variable - won't cut of last character anymore
* bugfix:   fixed many structural HTML errors in the admin UI and other clean-up
* bugfix:   hopefully fixed admin menu "non-shows" for good
* other:    complete overhaul of the translation strings (major clean up)
* other:    enhanced cforms uninstall/cleanup routine
* other:    CSS adjustments for some themes; counteracting too aggressive WP themes killing the cform layouts
* other:    made several SQL calls more robust and less likely to cause SQL errors
* other:    enhanced dashboard support
* enhanced: the "simple example" for custom forms on help page (showing a more flexible and elegant way of handling custom form field arrays!)

= 5.52 =

* bugfix:   Date Picker: fixed critical bug appearing on non "forms pages"
* bugfix:   Date Picker: fixed semi critical bug when showing multiple forms on the same page
* bugfix:   Dashboard feature:  fixed SQL error in case no forms have been submitted yet
* other:    XHTML strict: fixed "name" attribut in form tag for compliance

= 5.51 =

* bugfix:   removed some debug code (echo) in T-A-F (disable/enable)
* bugfix:   fixed empty `<ol>` tag
* bugfix:   fixed admin menu for Windows IIS web server
* bugfix:   minor version display fix
* other:    JS file for "popup date picker" now only loaded when feature is enabled
* other:    renamed JS functions in "popup date picker" code (to avoid possible incompatibilities)
* other:    select boxes & upload fields now also show a "required" txt label

= 5.5 =

* feature:  special regexp use: compare two input fields for equal content (e.g. email verification)
* feature:  'Tell-A-Friend' enable all posts/pages per click
* feature:  'Tell-A-Friend' default behaviour for new posts/pages
* feature:  Fancy Javascript date picker
* feature:  "WP Comment/Message to author" Feature
* feature:  added dashboad support (showing last 5 entries)
* other:    since 2.3 comes with update support, I removed local update notification code (saves a few kb)
* bugfix:   corrected form layout when no FIELDSETS are being used
* bugfix:   radio button fix, in case no label/li ID's are enabled
* bugfix:   made some changes to session mgmt in favour of keeping form content when hitting the browsers back button
* bugfix:   fixed use of special character "." as an empty trailing line in TXT messages
* other:    tuned code a bit, hopefully with a performance gain
* bugfix:   fixed weird caching phenomena when deleting of forms
* bugfix:   fixed non ajax regexp processing

= 5.4 =

* feature:  added Tell-A-Friend functionality, see Help documentation
* feature:  added filter option for displaying data records on "Tracking" page
* feature:  added support for individual input field CSS customization ie. unique <li> ID's, see "Styling" page
* feature:  added ajax captcha reset
* feature:  added individual error messages (HTML enabled), see Help
* feature:  added HTML support for field labels (field names), see examples on Help page
* feature:  added HTML support for the general error and success message (HTML gets stripped for popup alert() boxes!)
* other:    changed {Page} variable to reflect query params (/?p=123) if using the default permalink structure
* other:    changed session_start() call in favour of gzip compression
* other:    forcing chronological order of data records when downloading as CSV
* bugfix:   fixed group check box bug (in ajax)
* bugfix:   fixed special characters (e.g. Umlauts) in subject line
* bugfix:   minor CSS bugs
* bugfix:   check box select bug on "Tracking" page
* bugfix:   fixed copying of attachment(s) to specified server dir, when Tracking is turned off
* bugfix:   fixed sorting bug on 'Tracking' page for Internet Explorer

= 5.3 =

* bugfix:   admin HTML with non auto conf. TXT email would cause flawed HTML CC email
* bugfix:   fixed mailer error messages for ajax (they would not show)
* other:    improved/simplified UI
* other:    lots of clean up and making UI around email messaging more obvious, hopefully

= 5.2 =

* feature:  support for alternative SMTP server
* feature:  post processing of submitted data (see documentation)
* enhanced: simplified, and this made non-HTML (=TXT) emails more robust
* enhanced: improved layout of textarea data (HTML) in admin emails
* bugfix:   stopped leaking HTML in TXT part of message
* bugfix:   fixed CC: feature for non-Ajax submissions
* other:    re-implemented ajax support now utilizing POST to avoid any input limitations (# of characters)
* other:    more robust email address/name processing

= 5.1 =

* feature:  FROM: address can again be changed via UI, BE CAREFUL!
* feature:  added hook for outside processing/manipulation of form data
* bugfix:   Outlook (especially 2007) requires special HTML formatting
* other:    bit of code clean up here and there

= 5.0 =

* feature:  added a couple of CSS Themes
* feature:  multiple upload fields in the same form now supported
* feature:  3rd party email tracking support, e.g. readnotify & didtheyreadit
* feature:  basic widget support (make sure to double check Theme CSS!)
* feature:  alternative form action supported (please read config info!)
* feature:  BCC to copy additional admin(s)
* feature:  additional themes: blue & green
* feature:  full [additional] HTML formatting support for email messages
* bugfix:   BACKUP & RESTORE fixed (not all fields were backed-up prev.)
* bugfix:   "spacing between labels & data" error when number smaller than length(field name)
* bugfix:   more CSS corrections
* bugfix:   the use of single & double quotes fixed in FIELDSETS
* bugfix:   one more single quote bug remedied in form labels
* bugfix:   DB tracking of user verification input now consistent w/ and w/o Ajax mode
* bugfix:   critical CAPTCHA issue resolved when more than one CAPTCHA fields are displayed on the same page
* bugfix:   a mail server error would cause a bogus redirect and on top "hide" the actual error making any troubleshooting virtually impossible
* bugfix:   critical javascript error when using more than 9 forms
* bugfix:   regexp in non-ajax mode cause an error when using a slash '/'
* other:    layout enhancements for all CSS Themes
* other:    default variables fixed for auto confirmation message (subject & message)
* other:    code clean up & major admin usability/accessibility improvements
* other:    fixed leading _ in form object ID's
* other:    now validates for XHTML 1.0 "Strict", too

= 4.8 =

* other:    added optional credit text - if you're happy with cforms you may want to leave it enabled
* feature:  added a configurable SPACE between labels & data in the form email
* feature:  file uploads (form attachments) can now optionally be excluded from the email. They can be downloaded via "Tracking" (if enabled!) or accessed directly on the server
* bugfix:   properly escaped subject lines (when using visitor defined subject)
* bugfix:   fixed single quotes in field names
* bugfix:   text-only fields would falsely be added to the Tracking Tables
* bugfix:   non Ajax method: possible formatting issues with 1st fieldset in email
* bugfix:   non Ajax method: DB tracking of check boxes corrupted
* bugfix:   Ajax method: fixed possible "Multi-Recipients" bug
* bugfix:   non Ajax method: added a missing error message for failed attempts email forms
* bugfix:   DB Tracking: multi-line fields are now consistently stored (no extra <br/>)
* other:    a few more form themes (wide & big, no border)
* other:    slightly enhanced email formatting
* other:    added separate USER CAPability for tracking only! (use w/ Role Manager plugin!)

= 4.7 =

* bugfix:   field names would not show correctly when upgrading from 3.x to 4.6+
* bugfix:   simple CSS changes to support Opera Browsers (tested on 9+)
* other:    made some captcha mods for better readability

= 4.6 =

* feature:  page redirect on successful form submission
* feature:  customizable admin form email (header, subject)
* feature:  customizable auto confirmation message (input field reference) & pre-defined variables
* bugfix:   multiple, sequentially arranged check box groups would "collapse"
* bugfix:   fixed adding/duplicating new forms with WP2.2 (WP caching issue)
* bugfix:   db tracking in non-Ajax mode showed inconsistent input field names
* other:    made the DB tracking tables creation process more flexible, hopefully avoiding "CURRENT_TIMESTAMP" err msgs in the future!

= 4.5 =

* enhanced: the format for check box groups has been enhanced, see HELP!
* feature:  (optional) ID's for labels for even greater level of customization!
* bugfix:   "Subject for Email" could not be saved "Is Required"
* other:    "Subject for Email", user definable subject is now appended
* other:    "Subject for Email" is now part of the email form submission body
* other:    form structure re-done! XHTML'fied; much more robust now
* other:    streamlined CSS
* other:    added a warning msg re: "Show messages" settings

= 4.1 =

* feature:  support for shown but disabled form element
* feature:  "user message" positioning, now optionally at the bottom of the form
* feature:  "multi-select" via check boxes, grouped check boxes
* feature:  new special field: subject field
* other:    revised and cleaned up Help! section

= 4 =

* feature:  captcha support for additional SPAM protection
* feature:  select & configure stylesheets via admin UI
* bugfix:   IE margin-bottom hover bug
* bugfix:   deleting form fields (on the general form config page) was broken due a new bug that was introduced as part of the localization effort
* other:    change the INSERT queries using LAST_INSERT_ID() due to overly sensitive SQL servers.

= 3.5 =

* feature:  slightly enhanced Tracking page ("delete" now also removes attachments). Tracking data view now permits selective deletion of submission entries
* feature:  text fields can optionally be auto cleared on focus (if browser is JS enabled)
* feature:  attachments (uploads) are now stored on the server and can be accessed via the "Tracking" page
* feature:  added optional ID tracking to forms (& emails sent out)
* bugfix:   editor button wouldn't show due to wrong image path
* bugfix:   order of fields on the "Tracking" page fixed, to ensure an absolute order
* bugfix:   due to a WP bug, the use of plugin_basename had to be adjusted
* bugfix:   fixed support for non-utf8 blogs ( mb_convert_encoding etc.)
* other:    code cleanup (big thanks to Sven!) to allow proper localization current languages supported: English, default; German, provided by Sven Wappler
* other:    changed data counter (column 1) on the Tracking page to reflect unique form submission ID, that a visitor could possibly reference.

= 3.4 =

* feature:  multi-select fields
* feature:  dynamic forms (on the fly form creation)
* bugfix:   minor display bug on admin page: "add new field" button
* bugfix:   fixed a CSS bug to better support 3 column WP themes (w/ middle column not floated)

= 3.3 =

* feature:  "file upload field" can now be mandatory
* feature:  additional select box for more intuitive form selection
* enhanced: drop down "-" option for multi recipients
* bugfix:   select (drop down) boxes did not save values for non ajax method
* bugfix:   when using "multi-recipients" field & first entry used, email would still go out to everyone
* bugfix:   charsets other than UTF-8 caused issues with special characters in emails
* other:    added form name as hover text for form buttons

= 3.2.2 =

* feature:  most attachment types (images, docs etc) are now recognized
* bugfix:   not really a bug, but no "extra" attachments anymore
* bugfix:   more special characters in response messages

= 3.2 =

* feature:  file upload; only works with non-ajax send method (chosen autoamtically) due to HTML constraints. ajax support does NOT need to be explicitly disabled
* feature:  select boxes (drop downs) now can be "required" -> to support situations, where you don't want a default value to kick in, but want the visito to make a choice! See HELP! section for more info on how to use this new feature
* feature:  checkboxes : now can be "required" -> for "I have read the above" type scenarios, where the user has to comply/agree to a statement
* feature:  radio buttons, you can now click on the labels to toggle the selection
* feature:  radio & select boxes (drop down): now accept a "display value" & a "submit value". See HELP! section for more info
* feature:  "submit button" is now disabled after sending to prevent multiple submissions in case the web servers response is delayed (Ajax!)
* feature:  download supports both CSV and TXT (tab delimited)
* bugfix:   time correction in email (now considers blog time/date configuration)
* bugfix:   failure and success msgs would not show special characters properly
* bugfix:   “database tables found msg” would always show when settings were saved
* bugfix:   labels (left of an input field) would not display special chars correctly
* other:    renamed a few functions to avoid conflicts with other plugins
* other:    modified checkboxes: text to the right is by default "clickable"
* other:    W3C XHTML compliance now fully supported even when using REG EXPRESSIONS!

= 3 =

* bugfix:   changed the priority of the plugin: fixes layout issues due to wpautop
* bugfix:   fixed ajax (email) issues with CC: and Visitor verification fields
* bugfix:   fixed a few minor layout issues
* feature:  new admin uinterface
* feature:  full support for role-manager
* feature:  database tracking of form input & download as a CSV file
* feature:  backup and restore individual form settings (doesn't affect plugin-wide settings)
* feature:  erase all cforms data before deactivating/uninstalling the plugin
* feature:  added a new special field: "textonly" to add fully customizable paragraphs to your forms
* feature:  verification question to counteract spam
* feature:  custom regular expressions for single line input fields. usage: separate regexp via pipe '|' symbol:  fieldname|defaultval|regexp. e.g. Phone|+49|^\+?[0-9- \(\)]+$
* feature:  new menu structure (now top level menu!)
* other:    admin code clean up
* enhanced: verification codes accept answers case insensitive

= 2.5 =

* feature:  multiple email recipients ("form admins"): mass sending & selective sending by (visitor)
* feature:  CFORMS.CSS includes custom settings for form #2 (to see it in action, create a second form (#2) with one FIELDSET and a few input fields)
* feature:  order of fields; fields can now be sorted via drag & drop
* feature:  forms can be duplicated
* feature:  Fully integrated with TinyMCE & code editor. FF: hover over form placeholder and form object will be displayed. IE: select form placeholder and click on the cforms editor button
* feature:  default values for line & multi line input fields: use a "|" as a delimiter
* enhanced: "Update Settings" returns directly to config section
* bugfix:   quotes and single quotes in input fields fixed
* bugfix:   adding/deleting fields will respect (=save) other changes made
* feature:  all form fields can now be deleted up until the last field
* feature:  CC optional for visitor / if CC'ed not auto confirmatin will be sent add'l
* feature:  enhanced email layout - supporting defined fieldset
* feature:  REPLY-TO set for emails to both form admins & visitors (CC'ed)
* enhanced: non ajax form submission: page reloads and now jumps directly to form (& success msg)
* enhanced: code clean up and a handful of minor big fixes

= 2.1.1 =

* bugfix:   IE not showing AJAX / popup message stati
* bugfix:   send button jumping to the left after submitting
* feature:  check boxes: text can now be displayed both to the left and right

= 2.1 =

* feature:  fieldsets are now supported: CSS: .cformfieldsets addresses all sets, cformfieldsetX (with X=1,2,3...) individual ones.
* enhanced: form code clean-up: more standardized with a minimum on necessary elements and got rid of all the legacy DIVs
* enhanced: javascript has been "outsourced" making your html so much nicer :)

= 2 =

* feature:  additional form fields: checkboxes, radio buttons and select fields. Please note the expected "Field Name" entry format, separating input field items form the field name: i.e. radio buttons: field-name#button1#button2#button3#...
* enhanced: ajax support can be optionally turned off
* enhanced: a form can now have as few input fields as two
* enhanced: more flexibility in choosing email entry field. NOTE: if you have multiple email fields in your form, only the first will be used for sending the auto confirmation to
* feature:  "valid email required" placeholder added to indicate required input format for email fields
* feature:  optional popup window for user messages (may be helpful for very long forms)
* other:    code cleanup

= 1.90 =

* bugfix:   email header correction: "From:" doesn't claim to be visitor's email address anymore this should fix most paranoid mail server

= 1.81 =

* feature:  form name added for either email filtering or simply better differentiation
* feature:  admin email: can now be just "xx@yy.zz" or "abc <xx@yy.zz>" (from name removed)
* enhanced: changes to email header: simplified and "WP compliant"
* feature:  added to cforms.css: success and failure styles
* bugfix:   bug fix related to the use of a single forms (#2 and up) and insertion of ajax code
* bugfix:   FINALLY fixed "CR"s for multi-line response messages (success & failure fields)

= 1.71 =

* bugfix:   HTML bug resolved & localization for "waiting message"
* enhanced: default value for email recipient is now the blog admins' email address
* feature:  added a function call to insert form anywhere on your blog
* feature:  added new version support

= 1.6 =

* bugfix:   email/form functionality w/ standard send mechanism

= 1.5 =

* feature:  clean up, external css, multiple forms support & user auto confirmation
