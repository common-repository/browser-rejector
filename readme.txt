=== Browser Rejector ===
Contributors: celloexpressions
Tags: browser, browsers, rejector, rejecter, reject, rejection, browser reject, modern browser, html5, css3, modernizer
Requires at least: 3.3.0
Tested up to: 3.5.1
Stable tag: 3.0
Description: Notify, or optionally block, site visitors that they are using an outdated web browser, so you can use html5, css3, etc. freely.
License: GPLv2

== Description ==
The browser rejector plugin allows web designers to embrace emerging html5 and css3 technologies by blocking access or requiring an outdated browser acknowledgement to access your site, thus greatly reducing the need for backward compatibility measures. The rejection window overlays your website with a semi-transparent layer, and alternative browsers are dynamically suggested based on the current browser/OS (no Safari on Windows, no IE on Mac). Google Chrome Frame is offered for Internet Explorer users, and can be installed on locked-down systems; rendering pages as chrome does within any version of IE. The browsers are listed with icons and either the most-recent version or the solid html5 version+ (ex. IE9+) is displayed. Browsers to reject are chosen with checkboxes in the admin interface, with general html5/css3 compatibility listed inline. CSS classes (OS, Browser/Version, Rendering Engine/Version) are added to the html element as well, to allow for browser-specific styling.

This plugin is also great for anyone who want to help push the web forward by encouraging (or requiring) visitors to update to modern browser technology. Updates are released frequetly to list newer browser versions as they become available. The plugin defaults to rejecting all non-html5-compatible browsers, so it's a quick install if you have limited time.

Loosely based on the <a href="http://jreject.turnwheel.com/" target="_blank">jreject jquery plugin</a> by Steven Bower.

Please feel free to offer any feature suggestions you might have and I WILL consider them for future releases.

== Installation ==
1. Take the easy route and install through the wordpress plugin adder :) OR
1. Download the .zip file and upload the unzipped folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Make sure that your theme has the `<?php wp_head() ?>` and `<?php wp_footer() ?>` action hooks
1. Choose the browsers to reject and set the rejection window text, OR sit back and relax with the defaults.

== Frequently Asked Questions ==
= Rejecting vs. Blocking =
Visitors using rejected browsers can still access your site, but are initially presented with a prominent browser warning that overlays your site. Visitors with blocked browsers cannot dismiss the rejection modal, and will need to update or use another browser to access it.

= What if I want to completely block some browsers, but only show a warning for others? =
Set the plugin to allow visitors to close the window. At the bottom of the page an option to reject ie6/7/firefox 3 completely. If you want to completely block others, just enter them in the `browsername` or `browsernamev#` format in the field for browsers to block access on (for example, `safari` would block all versions of Safari that are being rejected at all, `safari5` would block Safari 5 if it is being rejected, including mobile versions of Safari). Be very careful with the restrictions of mobile browsers, for example, Internet Explorer 9 is the **only** browser available on Windows Phone 7, so blocking it would prevent visitors from accessing your site with their device. For historical reasons, the Android 3.x browser identifies itself as Safari 4. While Safari 4 should be rejected, Android shouldn't be.

= What If I only Want to Block IE6? =
No problem, just uncheck all of the other browsers in the Browsers to Reject tab.

= Where's the settings page? =
It's under settings: Settings -> Browser Rejector. And on a side note, the tabs on the settings page are showing different parts of the same page, not refreshing the browser, so you can switch between tabs without needing to save (and you are always returned to the rejection window tab when saving).

= Can I target OS versions with the CSS classes? =
Unfortunately, no. If it were possible, IE would be blocked on Windows XP as IE9+ is not available there. If anyone has a suggestion for how to accomplish this, please let me know! I might look into grabbing the Windows NT 6.x, etc. part of the user agent string...

= Why isn't Safari offered on Windows? =
As of Safari 6, Apple has quietly stopped development of Safari for Windows. Safari for Windows was never a serious contender compared with the other browsers anyway.

= Opera? And what about Konqueror? =
The Opera browser is much less known than Chrome, IE, Firefox, and Safari, but it does have some market share and is a solid modern browser around the IE9/Firefox overall quality level. Konqueror is a Linux broswer and is not included in the plugin options because it is so little-used. Versions 1-3 are rejected by the plugin automatically, however. Other more obsure browsers can be rejected by manually entering their rejection terms in the browsers to reject field.


== Screenshots ==
1. Default rejection window view (on Windows 7 in Chrome, so no Safari or Chrome Frame displayed)
2. Admin Screens
3. Admin Screens

== Changelog ==
= 3.0 =
* Added ability to specify custom browsers/versions to reject. These can be in the form of the browser name, name and version number, rendering engine, etc. and any browsers can be entered.
* Added ability to manually specify additional browsers to block completely.
* Updated Chrome version to 25
* Updated Firefox version to 19

= 2.11 =
* Fixed major security vulnerability, update immediately
* Confirmed WordPRess 3.5 Compatibility
* wp_footer() action hook is now required again
* Updated Firefox version to 17

= 2.10 =
* Updated Chrome Version to 23
* Compressed all plugin images to reduce plugin size by 330kb
* Fixed bug where IE9 was easily blocked (it's standards are sufficiently modern to not warrant blocking)

= 2.09 =
* Updated Internet Explorer Version to 10
* Updated Firefox Version to 16

= 2.08 =
* Added German translation (thanks to Axel Kriewel for providing)
* Placed the chromeframe activation tag in a conditional comment so the code validates (it only does stuff on IE anyway)
* Fixed IE link (Microsoft changed the url)
* Translations of browser names are now displayed in the output as well as the preview (names usually aren't translated, but for when they are)
* Fixed issue where an extra page opened every time a browser icon was clicked, this was something residual from the jreject jquery plugin
* Ability to reject IE9 has been implemented (in comments) in anticipation of IE10, to be released later this month. This ability will remain commented out because there is little good reason to block IE 9, but is there in case you need to distinguish between 9 and 10.
* Fixed a couple other minor bugs

= 2.07 =
* Fixed minor versioning error
* Updated Google Chrome to version 22

= 2.06 =
* Fixed a HUGE bug, and removed a ton of associated workarounds and bugs for IE 6 and 7
* Switched the icon set to not using transparent PNGs, because of a lack of suport by IE6 :( they are still PNGs which is the best graphics format for these icons though
* Improved the option to completely block access only on certain browsers, you can now choose ie6/7 and firefox3 independently (option is available only when allowing visitors to close the window universally)
* Removed dependence on wp_footer action hook, only wp_head is required now
* Officially dropping all suport for IE5 (which no one uses), as it isn't supported by jQuery
* Firefox version actually updated to 15 (oops!)
* Added the ability to translate (use the .pot and send me the .po and .mo if you'd like to help out)
* Wrote Spanish Translation (es_ES and es_PE)
* Added Persian Translation (thanks to Rezaei for writing)

= 2.05 =
* Fixed several minor bugs. Thanks to everyone who pointed them out either through the support forums or email!

= 2.03 =
* Added ability to let users close the rejection window on all browsers except IE6/7; this allows for greater flexibility depending on the severity of browser deprecation...

= 2.02 =
* Firefox version displays updated to 15. I know these details are somewhat OCD, but I will try to keep these updated as new browser versions are released. Let me know if I miss a release. Most future updates will be of this type.

= 2.01 = 
* Fixed a long list of bugs regarding rejection window display in Internet Explorer 6 and 7
* After studies with IE5, it has been determined that it will not be supported by this plugin actively. The browser is rejected, but the rejection window display is... weird (not that ANYONE uses IE5 anyway)

= 2.00 =
* Fixed major bug with rejection in IE 6 and 7.
* Automatically includes the code to render the site in chrome frame (when installed in IE), wp_head and wp_footer action hooks are now required
* A couple other minor adjustments

= 1.00 =
* First publically available version of the plugin.
* Compatible with Wordpress 3.3.0 through 3.4.0
* Listed browser versions include: Chrome 21, IE9, Firefox 14, Opera 12, and Safari 6

== Upgrade Notice ==
= 2.11 =
* Fixed major security vulnerability, update immediately. wp_footer() action hook is now required. Updated Firefox version to 17.