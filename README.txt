=== StreamWeasels Twitch Integration ===
Contributors: streamweasels, j.burleigh1, freemius
Tags: twitch, twitch streams, twitch api, twitch embed, twitch blocks
Requires at least: 5.0
Tested up to: 6.6
Stable tag: 1.9.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Embed Twitch streams with our collection of Twitch Blocks and Shortcodes. Works with Block Editor, Classic Editor, and Page Builders.

== Description ==

=== The most advanced Twitch plugin for WordPress ===

For over 8 years, [StreamWeasels](https://www.streamweasels.com?utm_source=wordpress&utm_medium=twitch-integration&utm_campaign=readme) have been helping thousands of WordPress websites **display twitch streams in their WordPress websites**.

StreamWeasels Twitch Integration is the latest and greatest plugin from StreamWeasels that **takes Twitch Integration to the next level**.

This plugin allows you to **display twitch streams anywhere on your website** in a variety of unique and professional-looking layouts.

=== Embed a Single Twitch Stream based on Channel ===

StreamWeasels Twitch Integration allows you to embed a single Twitch stream anywhere on your website with our easy-to-use Gutenberg block or shortcode.

* Embed a single stream with the following shortcode: [sw-embed channel="lirik"]
* No Twitch API connection required for a single stream embed
* Customise your embed with the following options:
* Autoplay
* Start Muted
* Embed Chat
* Colour Theme
* Embed Width
* Embed Height

=== Display Twitch Streams by Game, Channel List, Team and more ===

StreamWeasels Twitch Integration allows you to display groups of streams from Twitch based on Games, Channels, Teams, Languages, Titles and more.

* Embed a group of streams with the following shortcode: [sw-twitch channels="monstercat,lirik,sodapoppin"]
* Twitch API connection required for a group of streams embed
* Display upto 1000 streamers playing a specific **Game**.
* Display upto 1000 streamers from a specified **List of Channels**.
* Display upto 1000 streamers from a specific **Twitch Team**.
* Display only streams with a specific *tag* in the **Stream Title**.
* Display streams in a **specific language** only.

=== Advanced Combinations ===

You can combine our options for some very powerful Twitch Integrations. 

* Display all streamers from a **Twitch Team** only if they are playing a specific **Game**.
* Display all streamers from a **List of Channels** only if they are playing a specific **Game**.
* Display all streamers playing a specific **Game** but only if they have a specific **Tag** in their **Stream Title**.

=== Examples ===

Here are some real examples from some of the many StreamWeasels Twitch Integration users.

* Display all users from a **Twitch Team** but only if they're playing **League of Legends**.
* Display 100 **GTA V** Streamers but only if they have *NoPixel* in their **Stream Title**.
* Display all users from a **Twitch Team** but only if they have *#LGBTQ+* in their **Stream Title**.
* Display all streamers playing **Music** but only if they have *Requests* in their **Stream Title**.

=== Layouts ===

The best part about StreamWeasels Twitch Integration is our library of **Add-on Plugins**. Our **free** Add-ons allow you to unlock a variety of different layouts for your Twitch streams.

=== Free Layouts ===

* [[Layout] Twitch Wall](https://wordpress.org/plugins/ttv-easy-embed-wall/). Display a large number of streams all on one page, just like Twitch.
* [[Layout] Twitch Player](https://wordpress.org/plugins/ttv-easy-embed-player/). Display a any number of streams in a small space, with a scrolling sidebar and space for the embed.
* [[Layout] Twitch Rail](https://wordpress.org/plugins/ttv-easy-embed/). Display a large number of streams in a tiny space, with the ability to swipe left and right.
* [[Layout] Twitch Status](https://wordpress.org/plugins/stream-status-for-twitch/). Simply display your Twitch live status on every page of your website.
* [[Layout] Twitch Vods](https://www.streamweasels.com/product/twitch-vods/?utm_source=wordpress&utm_medium=twitch-integration&utm_campaign=readme). The easiest way to display Twitch VODS on your website.

=== PRO Layouts ===

* [[Layout] Twitch Feature](https://www.streamweasels.com/product/twitch-feature/?utm_source=wordpress&utm_medium=twitch-integration&utm_campaign=readme). Slick, professional-style layout inspired by the Twitch homepage.
* [[Layout] Twitch Nav](https://www.streamweasels.com/product/twitch-nav/?utm_source=wordpress&utm_medium=twitch-integration&utm_campaign=readme). The easiest way to display Twitch status in your main navigation.
* [[Layout] Twitch Showcase](https://www.streamweasels.com/product/twitch-showcase/?utm_source=wordpress&utm_medium=twitch-integration&utm_campaign=readme). Professional eSports-inspired layout.

=== Read More ===

If you want to learn more about StreamWeasels Twitch Integration, check out these links.

* [Twitch Integration - Getting Started Guide](https://support.streamweasels.com/article/22-getting-started-with-twitch-integration)
* [Check out our YouTube Guides](https://www.youtube.com/channel/UCo885jUiOeyhtHDFUbdx8rQ)
* [Follow us on Twitter](https://twitter.com/StreamWeasels)
* [Join us on Discord](https://discord.com/invite/HSwfPbm)
* [Need Help? Get in touch!](https://www.streamweasels.com/contact/)

== Frequently Asked Questions ==

= How do I display streamers playing a specific Game? =

[sw-twitch game="GTA V"]

= How do I display streamers playing a specific Team? =

[sw-twitch game="ths"]

= How do I display streamers from a Channel List? =

[sw-twitch channels="lirik,shroud,sodapoppin"]

= How do I filter streams based on their Stream Title? =

[sw-twitch game="GTA V" title-filter="NoPixel"]

= How do I display only streams from a specific language? =

[sw-twitch game="Hearthstone" language="de"]

== Screenshots ==

1. Twitch Wall (included)
2. Twitch Rail (included)
3. Twitch Player (included)
4. Twitch Status (included)
5. Twitch Feature (paid)
6. Twitch Showcase (paid)
7. Twitch Nav (paid)
8. Twitch Vods (included)

== Changelog ==

= 1.9.0 =
* updated freemius

= 1.8.9 =
* Nonce generation moved to server side to avoid caching issues

= 1.8.8 =
* Default iframe width and height if not provided
* Allow either true or 1 to be used for shortcode attributes

= 1.8.7 =
* Properly sanitize and escape all output from sw-twitch-embed shortcode
* iframes within Twitch embeds are now 100% width and height

= 1.8.6 =
* updated freemius
* added new option to bypass nonce checks for better compatibility with caching plugins

= 1.8.5 =
* Added new skew effect to Feature layout
* Fixed some styling issues with Feature layout

= 1.8.4 =
* Added alert for new Status Bar plugin
* updated freemius

= 1.8.3 =
* rest api endpoint now works with subpages
* updated freemius

= 1.8.2 =
* updating freemius

= 1.8.1 =
* API requests moved from client side to server side
* Added PHP 8.x support

= 1.8.0 =
* Fixed an issue with broken sw-twitch-embed shortcodes and blocks

= 1.7.9 =
* added some missing escaping and sanitisation
* added user and permissions check to endpoint
* cleaned up some older / unused code

= 1.7.8 =
* Added logged-in check to endpoint

= 1.7.7 =
* Display Twitch clips from multiple users on a single wall

= 1.7.6 =
* data sanitisation for shortcode attributes

= 1.7.5 =
* CSS fixes for the Feature layout on mobile devices (<530px)

= 1.7.4 =
* Added a new autoplay-offline option

= 1.7.3 =
* CSS variables shifted to kebab case to better support html minification

= 1.7.2 =
* Added a new banner for Kick plugin
* Debug log will now trim itself when it gets too big
* Debug log will now be emptied when the plugin is de-activated

= 1.7.1 =
* You can now disable auto-scroll on Wall layout with disable-scroll="1" on the shortcode

= 1.7.0 =
* Added new 'refresh' option to update Twitch Wall periodically
* Embedded iFrame will now only load if it doesn't already exist
* Bug fix for feature layout when settings not saved

= 1.6.8 =
* updated freemius
* feature and showcase layouts now available via blocks

= 1.6.7 =
* Only include PRO files if they exist!

= 1.6.6 =
* Users with an active license for the add-on plugins can now continue to use them as normal

= 1.6.5 =
* Twitch Feature is now bundled with PRO.
* Twitch Showcase is now bundled with PRO.
* Twitch Nav is now bundled with PRO.

= 1.6.3 =
* Twitch Vods is now FREE and part of the main plugin, the Twitch Vods Add-on can safely be disabled
* Twitch Vods now available via Blocks!
* Twitch Vods channels can now be set on the shortcode as vods-channel="", channels="" or channel=""

= 1.6.2 =
* Twitch Status is now part of the main plugin, the Twitch Status Add-on can safely be disabled
* Twitch Status now available via Blocks!
* Twitch Status now has a placement option (absolute or static)
* Referances to Add-ons changes to Layouts
* Referances to the streamweasels shortcode changed to sw-twitch
* Channels can now be set on the shortcode as either channels="" or channel=""

= 1.6.1 =
* Twitch Integration now available via Blocks!
* New Twitch Embed block added for simple single stream embeds!
* New Twitch Embed shortcode added [sw-twitch-embed]
* Existing Twitch Integration shortcode can now be shortened to [sw-twitch]
* Twitch Wall is now part of the main plugin, the Twitch Wall Add-on can safely be disabled
* Twitch Rail is now part of the main plugin, the Twitch Rail Add-on can safely be disabled
* Twitch Player is now part of the main plugin, the Twitch Player Add-on can safely be disabled
* Upgraded freemius to 2.5.9


= 1.5.9 =
* Added a permanent dismiss for the YouTube plugin notice
* Fixed the broken Twitch logo
* Upgraded freemius to 2.5.6

= 1.5.8 =
* Updated freemius to 2.5.3
* Added fix for uppercase language codes
* Added fix for featured-stream
* Added fix for feature layout and autoplay inside

= 1.5.7 =
* Now compatible with Block themes

= 1.5.6 =
* Upgraded licensing logic
* Upgraded freemius integration

= 1.5.5 =
* Added new messaging for YouTube plugin

= 1.5.4 =
* Added Discord messaging
* added channel names to HTML to allow for CSS selectors

= 1.5.3 =
* Added bundle and updated messaging

= 1.5.1 =
* Removed a transient check from Twitch Integration
* Fixed a bug with autoplay combined with link to Twitch

= 1.5.0 =
* default streams now pulled from Twitch ig game, team or channels left empty

= 1.4.9 =
* Twitch Showcase launch
* bumped tested to 6.0
* admin messaging tweaks

= 1.4.7 =
* Laying the groundwork for new Showcase add-on
* Made some improvements to Vods add-on
* Hotfix for showcase issue

= 1.4.2 =
* minor admin tweaks

= 1.4.1 =
* added fixes for Twitch Vods add-on
* changed the stream count to 15

= 1.4.0 =
* Fixed an issue related to game + channels combination
* Added new plan levels
* Added bundles

= 1.3.7 =
* Added support for Twitch Vods add-on
* Better handling of Game + Title filter
* Added dev variables

= 1.3.4 =
* Freemius SDK update

= 1.3.3 =
* Added Twitch Nav add-on to add-on section

= 1.3.2 =
* Added the foundation for Twitch Offline add-on

= 1.3.1 =
* Added the foundation for Twitch Nav add-on

= 1.3.0 =
* Fixed an issue with embed inside for Twitch Integration free users
* Fixed an issue with Twitch Feature PRO not initialising correctly

= 1.2.8 =
* Added the foundation for Twitch Live Status add-on
* Fixed shortcode links
* Fixed issue with free live info field

= 1.2.7 =
* Twitch Auth token won't try and generate if client ID and Secret empty
* Updated Twitch Auth tokens to regenerate automatically
* Fixed broken Twitch icon in chrome
* Fixed issue blocking multiple integrations to be placed on one page.
* Fixed wrong Image dimensions listed on Offline Image.
* Fixed Chat option now forced as (int).
* Fixed conflict with two Offline Image fields.
* Fixed issue with embed-chat shortcode attr
* Added can_use_premium_code() checks
* Fixed issue with game and tile sorting = least
* Fixed an issue with game names including & and other special characters
* Added output buffering
* Added new hover state (play button)
* Added new Live Info option
* Added string translations
* Feattured Streamer now placed at the top of stream list if all users are Offline
* Colour Theme field added for free users
* Fixed dimensions of embed popup on large screens
* Updated support links
* Added Refresh Token button
* removed get_transient call
* improved the logic around getting auth Token
* added simple / advanced shortcode descriptions
* added support for Feature PRO inside embed