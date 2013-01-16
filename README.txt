=== WP Audio Player ===
Contributors: tommcfarlin
Tags: mp3, audio, player
Requires at least: 3.5
Tested up to: 3.5
Stable tag: 1.3

WP Audio Player is a plugin based on the popular player from the guys at Codrops that aims to make it easy to include an audio player in your post.

== Description ==

WP Audio Player is a plugin that brings the <a href="http://tympanus.net/codrops/2012/12/04/responsive-touch-friendly-audio-player/">Responsive and Touch-Friendly Audio Player</a> from Codrops to WordPress.

It introduces a meta box to each post page that allows you to supply the URL to any audio file and will then append the player to the end of the post. This way, you're able to upload your media using the built in Media Uploader, and then use the provided URL to add the player to your post.

== Installation ==

= Using The WordPress Dashboard =

1. Navigate to the 'Add New' Plugin Dashboard
2. Select `wp-audio-player.zip` from your computer
3. Upload
4. Activate the plugin on the WordPress Plugin Dashboard

= Using FTP =

1. Extract `wp-audio-player.zip` to your computer
2. Upload the `wp-audio-player` directory to your `wp-content/plugins` directory
3. Activate the plugin on the WordPress Plugins dashboard

== Frequently Asked Questions ==

= Right now, the player only appears at the bottom of the post. Can I change it's position? =

In version 1.0, no; however, this is a planned feature assuming that the plugin is useful for other people.

== Screenshots ==

1. A screenshot of the plugin running on a live site at <a href="http://wpdaily.co/">WP Daily</a>.

== Changelog ==

= 1.3 =
* Properly escaping URL's when they are returned to the browser (Thanks <a href="https://github.com/tommcfarlin/wp-audio-player/pull/4">pdewouters</a>!)
* Improving the coding standards by some formatting tweeaks (Thanks <a href="https://github.com/tommcfarlin/wp-audio-player/pull/1">studioromeo</a>!)
* Resolving a problem that prevented the audio player from displaying in Firefox
* Updating the localization files
* Remove the donate link (because this project is now driven by the open source community)

= 1.2 =
* Adding support for custom post types and pages

= 1.1 =
* Making sure the player only displays on the single post page.

= 1.0 =
* Initial release