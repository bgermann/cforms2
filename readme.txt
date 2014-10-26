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


== Changelog ==

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

= 10.1 =

* feature:  added form option to turn off tracking for a given form only

* bugfix:   improved method of adding admin scripts/CSS (to better support preWP2.7 systems)
* bugfix:   added UI CSS mods to accomodate pre WP2.7 admin interface
* bugfix:   wrong dashboard icon extension (png vs gif)
* bugfix:   hover text over AJAX option on 'form settings'
* bugfix:   multi-page forms: after final submission, form #1 on occcasion would render only partly
* bugfix:   multi-page forms: if used with "WP comment form feature" at the same time, comment form would also be replaced with the current multi-part form
* bugfix:   multi-page forms: when deploying several MP form series the SESSION would in some cases not be reset
* bugfix:   in v10.0 access priviliges for tracking required to be "manage_cforms"

* other:    fixed CommentLuv support
* other:    WP2.7+ fixed admin UI : support for removeable top err/warning messages
* other:    a few minor UI adjustments to accomodate 1024px wide screens a tad better
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

* feature:  added restore function when cforms settings get corrupted (for whatever reason)
            the function kicks in as soon as cforms detects broken settings and will guide the user
* feature:  added form specific success and failure classes for more CSS/styling control
* feature:  added POP before SMTP authentication to SMTP feature
            (global settings)
* feature:  added filename modification for file uploads at run-time
            (see my-functions.php!)
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
* feature:  new custom function to control application logic during run-time
            my_cforms_logic() filter function, see my-functions.php
            for now only page redirection is being supported, more to come in the future
* feature:  revamped the code driving the deletion of forms (allowing also allowing the first form to be deleted)

* bugfix:   PHPMailer support for attachments (couldn't attach files to email)
* bugfix:   "Cannot use string offset as an array in lib_aux.php on line 12"
* bugfix:   minor bug in rendering nonAjax action path in form tag
* bugfix:   fixed paging on the tracking page
* bugfix:   fixed issue with cforms WP comment feature and additional fields (on top of
            the default comment fields) not showing up in notification email
* bugfix:   fixed CC:me field bug (for Ajax submission)
* bugfix:   form submission tracking : fixed dashboard & RSS link to actual form details
* bugfix:   fixed RSS support (mime types and general compatiblity issues)
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

* feature:  API function: get_cforms_entries( $fname, $from, $to, $sort, $limit )
            (see HELP for further info: "APIs...")
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

* feature:  added support for other plugins using TinyMCE (causing the 'purl' err currently).
            See buttons support, global settings.
* feature:  RSS feeds (security key enabled) for form submissions, global feed and for single forms
            See global settings and main form admin page.
* feature:  option to force CAPTCHA & Q&A even for logged in users
            See global settings.
* feature:  CAPTCHA now compatible with WP Super Cache plugin working with super cached pages!
* feature:  cforms "comment feature" now considers comment COOKIES (user preferences)

* bugfix:   fixed email (data) issue when CAPTCHA or Visitor Verification (Q&A) field was not
            at then end of the form
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
* other:    enhanced rendering of cforms comments (for WP post/page comments)
            see new comment template on global settings for new options and best pratice setup
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
* other:    added a patch to manage Wordpress annoying wp_autop 'feature' and thus
            fix xHTML validation (this should really be WP's task ;-)

= 8.5 =

* feature:  better custom-files support (CSS, CAPTCHA) to outwit the short comings of
            the WP auto update feature that removes/overwrites custom files
            *ALL custom files** should go into "/plugins/cforms-custom"
* feature:  added/changed default way of referencing forms, now: <!--cforms name="XYZ"-->
            for better transparency and persistence (when deleting forms)
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
* feature:  custom variables (referencing input field) can now be written as {_fieldXY}
            with XY being the input field no. as it appears in the admin form configuration, e.g
            {_field2}  =  2nd form field
            or even as {ID}  where id = [id:ID] when using custom IDS for your input fields
* feature:  enhanced custom input field names: if "[id:]" present in field name string,
            e.g. Your Name[id:fullname]|Your Name
            then what's given as the 'id' is being used for the fields id/name
* other:    changed focus to first missing/invalid input field, used to be the last field in
            the form
* bugfix:   checkboxgroup ID bug resolved (thanks Stephen!)
* other:    included a fix for other plugins that unnecessarily impose "prototpye" on all plugin
            pages

= 8.3 =

* feature:  Completely revised Tracking/Edit UI
* feature:  Tracking: XML download
* feature:  Tracking: Editable fields
* bugfix:   fixed IIS issues with CAPTCHA RESET
* bugfix:   datepicker default values (non-digit) would cause false start dates
* bugfix:   "page" wasn't properly recorded in some cases for ajax submission
* bugfix:   multiple upload fields: if the first field wasn't populated, none of the
            following attachments would be send in the email (but saved on the server)
* bugfix:   if all submissions were deleted from tracking tables, the first new form
            submission would be partially broken

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
* feature:  next to supporting $post custom fields, HIDDEN fields can no be fed via URL parameters,
            e.g.: URL?myVAR=test-string   | & the hidden field set to "myhiddenfield|<myVAR>"
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
* bugfix:   fixed "values" for checkboxes (Help! has been updated, too)
            if no value provided, 'X' is being used to indicate a checked box
            if a value is given, then that value is being used in the admin email
* other:    added <Line Break> capability to radio boxes!
* other:    REGEXP Validation: if present, validation *WILL* happen regardless of 'is required setting'

= 7.5 =

* feature:  WP comments feature completely revised
            +) no more dependency on wp-comments-post.php
            +) fully supporting comment form validation (esp. nonAjax!)
            +) Ajax'iefied

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
* other:    enabled structuring of drop down "select lists" with multiple `&nbsp;`, e.g.
            `item 1
            &nbsp;&nbsp;item 1.1
            item 2
            &nbsp;&nbsp;item 2.1
            &nbsp;&nbsp;&nbsp;item 2.1.1`

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
* other:    WP 2.3.2 certififed
* other:    Turkish language pack available

= 7.0 =

* feature:  much enhanced error display (optional)
            with direct links to erroneous entries,
            updated theme CSS (new styles),
            & embedded custom error messages!
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
* other:    unknown variables {xyz} would be removed, they're now left intact, supporting the use of:
            <style> p{blabla} </style> in your HTML notification messages!
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
* bugfix:   fixed TinyMCE compatiblity issues with WP2.0.2
* bugfix:   fixed wp_get_current_user() issue with WP2.0.2
* bugfix:   fixed tracking issue and {variable} bug when using fields with the same field label
            now, this works (example: checkbox group) and will be recorded properly:
            size#size1#size2#size3
            size#size4#size5#size6
            size#size6#size7#size8
* other:    reimplemented 'dashboard" support using activity box hook (no more JS)
* other:    changed behaviour of an email field. The "Email" flag doesn't anmore imply "Required"
* other:    Danish translation available!
* other:    Russian translation available!

= 6.1 =

* other:    more forgiving to IIS servers with 'a very special set of ENV variables'
            fixing a potential menu 'display bug'
* other:    combined WPMU and normal WP admin JS / to also cater to 'normal' WP installs
            specific prototype/jQuery usage
* other:    made cforms.js editbale to cater to specific IIS reqs (URI=...)
* bugfix:   form name would escape single quotes
* bugfix:   proper support for blogs with a URI prefix, e.g. /blog/wp-content/...
* bugfix:   added user rights check for dashboard display


== Localization ==

If your language is set correctly the language file in the ____Plugin_Localization directory should be picked up immediately.

If there is a language file available, but not included in the standard distribution, please put the cforms-*.mo file in the wp-content/languages/plugins directory.

You can find a list of the [translations for the original cforms version](http://www.deliciousdays.com/cforms-plugin/).

If you would like to contribute a new language file, please [file a ticket](https://github.com/bgermann/cforms2/issues/new) with the translation file attached.
It has to be GPL licensed. If an available translation on the original website is not GPL licensed,
you must not redistribute it as long as it is not your translation.


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
