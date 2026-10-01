=== Which Environment ===
Contributors: thartl
Donate link:
Tags:
Requires at least: 5.0
Tested up to: 6.8.1
Requires PHP: 7.4
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Displays site environment in the admin bar.  Activates and deactivates plugins and features based on current environment.

== Description ==

Displays site environment in the admin bar.  Activates and deactivates plugins and features based on current environment.

== Changelog ==

= 2.0.2 =
* Fixed: Admin file loading on WordPress installations using a relocated content directory.

= 2.0.1 =
* Fixed precedence between generic search-engine blocking and Google/Bing-only indexing prevention.
* Google/Bing-only mode now restores WordPress search visibility and takes precedence over the conflicting generic robots setting.

= 2.0.0 =
* Migrated plugin update distribution from Bitbucket to public GitHub.
* Updated YahnisElsts/plugin-update-checker from 5.6 to 5.7.

= 1.9.6 =
* Added an option to prevent Google and Bing from indexing non-live environments without changing the global WordPress search-engine visibility setting.

= 1.9.5 =
* Updated YahnisElsts/plugin-update-checker from 5.4 to 5.6

= 1.9.4 =
* Fixed PHP warning - Implicit conversion from float to int

= 1.9.3 =
* Removed Snapshot Pro options
* Added ACF Pro forced activation for live sites

= 1.9.2 =
* Adjusted admin menu hook priority for compatibility with WordPress 6.6
* Updated YahnisElsts/plugin-update-checker from 5.1 to 5.4

= 1.9.1 =
* Updated YahnisElsts/plugin-update-checker from 5.0 to 5.1

= 1.9.0 =
* Added Devilbox environment
* Updated YahnisElsts/plugin-update-checker from 4.13 to 5.0
* Added a filter to exclude unused detection strategies

= 1.8.1 =
* Updated YahnisElsts/plugin-update-checker from 4.11 to 4.13

= 1.8.0 =
* Removed WP Engine detection
* Removed Dev, Legacy staging, and Unknown environment detection
* Adjusted Staging styles

= 1.7.1 =
* Updated YahnisElsts/plugin-update-checker from 4.7 to 4.11

= 1.7.0 =
* Enhancement - Add options to force robots' indexing ON and OFF, based on environment.

= 1.6.5 =
* Fix - Some instances of 1.6.4 in the wild used the wrong version of YahnisElsts/plugin-update-checker.

= 1.6.4 =
* Updated YahnisElsts/plugin-update-checker to 4.7

= 1.6.3 =
* Fix - Check if array keys exist before accessing them

= 1.6.2 =
* New page title and menu title: Environment options

= 1.6.1 =
* Avoided future bug with nested function declaration + now running Snapshot deactivation immediately

= 1.6 =
* Added Snapshot Pro functionality

= 1.5.3 =
* Stripe test mode change log now saved in its own option + other fixes

= 1.5.2 =
* Fix PHP error

= 1.5.1 =
* Fix PHP error

= 1.5 =
* First stable update mechanism

= 1.1 - 1.4.10 =
* Dev

= 1.0 =
* Initial release
