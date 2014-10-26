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

= 6.0 =

* added:    TinyMCE: enhanced visual appearance of form placeholder in TinyMCE editor
* added:    TinyMCE: much improved TinyMCE & std editor button/insert dialog
            (now with direct form select & fully localize-able )
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
* other:    CSS adjustments for some themes; counteracting too aggressive WP themes
            killing the cform layouts
* other:    made several SQL calls more robust and less likely to cause SQL errors
* other:    enhanced dashboard support

* enhanced: the "simple example" for custom forms on help page (showing a more
            flexible and elegant way of handling custom form field arrays!)

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
* other:    renamed JS functions in "popup date picker" code (to avoid possible incompatiblities)
* other:    select boxes & upload fields now also show a "required" txt label

= 5.5 =

* feature:  special regexp use: compare two input fields for equal content (e.g. email verification)
* feature:  'Tell-A-Friend' enable all posts/pages per click
* feature:  'Tell-A-Friend' default behaviour for new posts/pages
* feature:  Fancy Javascript date picker
* feature:  "WP Comment/Message to author" Feature
* feature:  added dashboad support (showing last 5 entries)
* other:    since 2.3 comes with update support,
            I removed local update notification code (saves a few kb)
* bugfix:   corrected form layout when no FIELDSETS are being used
* bugfix:   radio button fix, in case no label/li ID's are enabled
* bugfix:   made some changes to session mgmt in favour of keeping form content
            when hitting the browsers back button
* bugfix:   fixed use of special character "." as an empty trailing line in TXT messages
* other:    tuned code a bit, hopefully with a performance gain
* bugfix:   fixed weird caching phenomena when deleting of forms
* bugfix:   fixed non ajax regexp processing

= 5.4 =

* feature:  added Tell-A-Friend functionality, see Help documentation
* feature:  added filter option for displaying data records on "Tracking" page
* feature:  added support for individual input field CSS customization
            ie. unique <li> ID's, see "Styling" page
* feature:  added ajax captcha reset
* feature:  added individual error messages (HTML enabled), see Help
* feature:  added HTML support for field labels (field names), see examples on Help page
* feature:  added HTML support for the general error and success message
           (HTML gets stripped for popup alert() boxes!)

* other:    changed {Page} variable to reflect query params (/?p=123)
            if using the default permalink structure
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
* bugfix:   fixed mailer error messsages for ajax (they would not show)
* other:    improved/simplified UI
* other:    lots of clean up and making UI around email messaging more obvious, hopefully

= 5.2 =

* feature:  support for alternative SMTP server
            Note: Due to an obvious WP bug, class-smtp.php needs to be renamed to class.smtp.php
* feature:  post processing of submitted data (see documentation)
* enhanced: simplified, and this made non-HTML (=TXT) emails more robust
* enhanced: improved layout of textarea data (HTML) in admin emails
* bugfix:   stopped leaking HTML in TXT part of message
* bugfix:   fixed CC: feature for non-Ajax submissions
* other:    re-implemented ajax support now utilizing POST to
            avoid any input limitations (# of characters)
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
* bugfix:   critical CAPTCHA issue resolved when more than one CAPTCHA fields are
            displayed on the same page
* bugfix:   a mail server error would cause a bogus redirect and on top "hide" the actual
            error making any troubleshooting virtually impossible
* bugfix:   critical javascript error when using more than 9 forms
* bugfix:   regexp in non-ajax mode cause an error when using a slash '/'

* other:    layout enhancements for all CSS Themes
* other:    default variables fixed for auto confirmation message (subject & message)
* other:    code clean up & major admin usability/accessibility improvements
* other:    fixed leading _ in form object ID's
* other:    now validates for XHTML 1.0 "Strict", too

= 4.8 =

* other:    added optional credit text - if you're happy with cforms you may want to
            leave it enabled

* feature:  added a configurable SPACE between labels & data in the form email
* feature:  file uploads (form attachments) can now optionally be exlcuded from the email
            they can be downloaded via "Tracking" (if enabled!) or accessed directly on the server
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
* other:    added seperate USER CAPability for tracking only! (use w/ Role Manager plugin!)

= 4.7 =

* bugfix:   field names would not show correctly when upgrading from 3.x to 4.6+
* bugfix:   simple CSS changes to support Opera Browsers (tested on 9+)
* other:    made some captcha mods for better readability

= 4.6 =

* feature:  page redirect on successful form submission
* feature:  customizable admin form email (header, subject)
* feature:  customizable auto confirmation message
            (input field reference) & pre-defined variables
* bugfix:   multiple, sequentially arranged check box groups would "collapse"
* bugfix:   fixed adding/duplicating new forms with WP2.2 (WP caching issue)
* bugfix:   db tracking in non-Ajax mode showed inconsistent input field names
* other:    made the DB tracking tables creation process more flexible, hopefully
            avoiding "CURRENT_TIMESTAMP" err msgs in the future!

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
* bugfix:   deleting form fields (on the general form config page) was broken due a
            new bug that was introduced as part of the localization effort
* other:    change the INSERT queries using LAST_INSERT_ID() due to overly sensitive
            SQL servers.

= 3.5 =

* feature:  slightly enhanced Tracking page ("delete" now also removes attachments)
            tracking data view now permits selective deletion of submission entries
* feature:  text fields can optionally be auto cleared on focus (if browser is JS enabled)
* feature:  attachments (uploads) are now stored on the server and can be accessed
            via the "Tracking" page
* feature:  added optional ID tracking to forms (& emails sent out)
* bugfix:   editor button wouldn't show due to wrong image path
* bugfix:   order of fields on the "Tracking" page fixed, to ensure an absolute order
* bugfix:   due to a WP bug, the use of plugin_basename had to be adjusted
* bugfix:   fixed support for non-utf8 blogs ( mb_convert_encoding etc.)
* other:    code cleanup (big thanks to Sven!) to allow proper localization
            current languages supported:
            English, default
            German, provided by Sven Wappler
* other:    changed data counter (column 1) on the Tracking page to reflect unique
            form submission ID, that a visitor could possibly reference.

= 3.4 =

* feature:  multi-select fields
* feature:  dynamic forms (on the fly form creation)
* bugfix:   minor display bug on admin page: "add new field" button
* bugfix:   fixed a CSS bug to better support 3 column WP themes
            (w/ middle column not floated)

= 3.3 =

* feature:  "file upload field" can now be mandatory
* feature:  additional select box for more intuitive form selection
* enhanced: drop down "-" option for multi recipients
* bugfix:   select (drop down) boxes did not save values for non ajax method
* bugfix:   when using "multi-recipients" field & first entry used, email would
            still go out to everyone
* bugfix:   charsets other than UTF-8 caused issues with special characters in emails
* other:    added form name as hover text for form buttons

= 3.2.2 =

* feature:  most attachment types (images, docs etc) are now recognized
* bugfix:   not really a bug, but no "extra" attachments anymore
* bugfix:   more special characters in response messages

= 3.2 =

* feature:  file upload; only works with non-ajax send method (chosen autoamtically)
            due to HTML constraints. ajax support does NOT need to be explicitly disabled
* feature:  select boxes (drop downs) now can be "required" -> to support situations,
            where you don't want a default value to kick in, but want the visito to make a choice!
            see HELP! section for more info on how to use this new feature
* feature:  checkboxes : now can be "required" -> for "I have read the above" type
            scenarious, where the user has to comply/agree to a statement
* feature:  radio buttons, you can now click on the labels to toggle the selection
* feature:  radio & select boxes (drop down): now accept a "display value" & a "submit value"
            see HELP! section for more info
* feature:  "submit button" is now disabled after sending to prevent multiple
            submissions in case the web servers response is delayed (Ajax!)
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
* feature:  full support for role-manager, see
            [here](http://www.im-web-gefunden.de/wordpress-plugins/role-manager/) for a current release
* feature:  database tracking of form input & download as a CSV file
* feature:  backup and restore individual form settings (doesn't affect plugin-wide settings)
* feature:  erase all cforms data before deactivating/uninstalling the plugin
* feature:  added a new special field: "textonly" to add fully customizable paragraphs to your forms
* feature:  verification question to counteract spam
* feature:  custom regular expressions for single line input fields
            usage: separate regexp via pipe '|' symbol:  fieldname|defaultval|regexp
            e.g. Phone|+49|^\+?[0-9- \(\)]+$
* feature:  new menu structure (now top level menu!)
* other:    admin code clean up
* enhanced: verification codes accept answers case insensitive

= 2.5 =

* feature:  multiple email recipients ("form admins"): mass sending & selective sending by
            (visitor)
* feature:  CFORMS.CSS includes custom settings for form #2 (to see it in action, create a second
            form (#2) with one FIELDSET and a few input fields)
* feature:  order of fields; fields can now be sorted via drag & drop
* feature:  forms can be duplicated
* feature:  Fully integrated with TinyMCE & code editor. FF: hover over form placeholder and form
            object will be displayed. IE: select form placeholder and click on the cforms editor
            button
* feature:  default values for line & multi line input fields: use a "|" as a delimiter
* enhanced: "Update Settings" returns directly to config section
* bugfix:   quotes and single quotes in input fields fixed
* bugfix:   adding/deleting fields will respect (=save) other changes made
* feature:  all form fields can now be deleted up until the last field
* feature:  CC optional for visitor / if CC'ed not auto confirmatin will be sent add'l
* feature:  enhanced email layout - supporting defined fieldset
* feature:  REPLY-TO set for emails to both form admins & visitors (CC'ed)
* enhanced: non ajax form submisssion: page reloads and now jumps directly to form (& success msg)
* enhanced: code clean up and a handful of minor big fixes

= 2.1.1 =

* bugfix:   IE not showing AJAX / popup message stati
* bugfix:   send button jumping to the left after submitting
* feature:  check boxes: text can now be displayed both to the left and right

= 2.1 =

* feature:  fieldsets are now supported: CSS: .cformfieldsets addresses all sets,
            cformfieldsetX (with X=1,2,3...) individual ones.
* enhanced: form code clean-up: more standardized with a minimum on necessary elements and
            got rid of all the legacy DIVs
* enhanced: javascript has been "outsourced" making your html so much nicer :)

= 2 =

* feature:  additional form fields: checkboxes, radio buttons and select fields.
            please note the expected "Field Name" entry format, separating input field items
            form the field name: i.e. radio buttons: field-name#button1#button2#button3#...
* enhanced: ajax support can be optionally turned off
* enhanced: a form can now have as few input fields as two
* enhanced: more flexibilty in choosing email entry field. NOTE: if you have multiple email
            fields in your form, only the first will be used for sending the auto confirmation to
* feature:  "valid email required" placeholder added to indicate required input format for email fields
* feature:  optional popup window for user messages (may be helpful for very long forms)
* other:    code cleanup

= 1.90 =

* bugfix:   email header correction: "From:" doesn't claim to be visitor's email
            address anymore this should fix most paranoid mail server

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
