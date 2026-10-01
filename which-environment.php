<?php
/*
 * Plugin Name: Which Environment
 * Description: Displays site environment in the admin bar.  Activates and deactivates plugins and features based on current environment.
 * Author: Tomas Hartl
 * Version: 2.0.2
 * Author URI: https://parkdalewire.com/
*/

use YahnisElsts\PluginUpdateChecker\v5\PucFactory;


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const WHICH_ENVIRONMENT_PLUGIN_VERSION = '2.0.2';


add_action( 'plugins_loaded', 'which_environment_load_plugin_update_checker' );
/**
 * Set up Plugin Update Checker.
 *
 * @return void
 */
function which_environment_load_plugin_update_checker() {

	if ( ! is_admin() ) {
		return;
	}

	// Plugin Update Checker GitHub integration
	require 'plugin-update-checker/plugin-update-checker.php';
	$whichEnvUpdateChecker = PucFactory::buildUpdateChecker(
		'https://github.com/thartl/which-environment-2',
		__FILE__,
		'which-environment'
	);

	// Set the branch that contains the stable release.
	$whichEnvUpdateChecker->setBranch( 'production' );
}



// Remove lock string used to prevent site URL from being updated during DB search and replace
// Used by functions in this file and by admin.php
function pw_decipher_locked_domain( $domain ) {

	return str_replace( '_[pw_site_domain_lock]_', '', $domain );
}


// Set up admin
if ( is_admin() ) {

	require_once plugin_dir_path( __FILE__ ) . 'admin.php';
}


// Get current domain
$th_site_url        = get_site_url();
$th_site_url_parsed = wp_parse_url( $th_site_url );
$th_current_host    = $th_site_url_parsed['host'];


// Get Live domain
$th_site_options = get_option( 'pw_site_options' );

if ( is_array( $th_site_options ) && array_key_exists( 'site_domain_locked', $th_site_options ) ) {

	$th_live_domain = pw_decipher_locked_domain( $th_site_options['site_domain_locked'] );

} else {

	$th_live_domain = '';
}


function th_is_this_live_site() {

	global $th_current_host, $th_live_domain;

	return $th_current_host == $th_live_domain;
}

function th_is_this_local_site() {

	global $th_current_host;

	$offset = strlen( $th_current_host ) - strpos( $th_current_host, '.local' );

	return 6 == $offset;
}

function th_is_this_devilbox_site() {

	global $th_current_host;

	$offset = strlen( $th_current_host ) - strpos( $th_current_host, '.loc' );

	return 4 == $offset;
}


/**
 ***************    Environment detection logic    ********************
 *
 */
if ( th_is_this_live_site() ) {

	$th_dev_environment   = 'Live site';
	$th_environment_title = 'You are on the Live site.';
	add_action( 'admin_enqueue_scripts', 'th_on_live_site_css' );
	add_action( 'wp_enqueue_scripts', 'th_on_live_site_css' );
	define( 'TH_WHICH_ENVIRONMENT', 'live' );
	th_force_stripe_to_live_mode();
	th_force_acf_pro_active();
	th_allow_robots();

} elseif ( th_is_this_local_site() ) {

	$th_dev_environment   = 'Local';
	$th_environment_title = 'You are in a local environment.';
	add_action( 'admin_enqueue_scripts', 'th_in_local_css' );
	add_action( 'wp_enqueue_scripts', 'th_in_local_css' );
	define( 'TH_WHICH_ENVIRONMENT', 'local' );
	th_stripe_to_test_mode();
	th_disallow_robots();
	th_prevent_search_engine_indexing();

} elseif ( th_is_this_devilbox_site() ) {

	$th_dev_environment   = 'Devilbox';
	$th_environment_title = 'You are in Devilbox.';
	add_action( 'admin_enqueue_scripts', 'th_in_devilbox_css' );
	add_action( 'wp_enqueue_scripts', 'th_in_devilbox_css' );
	define( 'TH_WHICH_ENVIRONMENT', 'devilbox' );
	th_stripe_to_test_mode();
	th_disallow_robots();
	th_prevent_search_engine_indexing();

} else {

	// Default: staging site
	$th_dev_environment   = 'Staging';
	$th_environment_title = 'You are on the Staging site.';
	add_action( 'admin_enqueue_scripts', 'th_on_staging_site_css' );
	add_action( 'wp_enqueue_scripts', 'th_on_staging_site_css' );
	define( 'TH_WHICH_ENVIRONMENT', 'staging' );
	th_stripe_to_test_mode();
	th_disallow_robots();
	th_prevent_search_engine_indexing();
}


// Switch Stripe on or off
function th_stripe_test_mode_switch_to_( $direction ) {

	// Turn on/off test mode
	$woocommerce_stripe_settings = get_option( 'woocommerce_stripe_settings' );
	if ( $woocommerce_stripe_settings && array_key_exists( 'testmode', $woocommerce_stripe_settings ) && $woocommerce_stripe_settings['testmode'] ) {

		if ( $direction != $woocommerce_stripe_settings['testmode'] ) {

			$woocommerce_stripe_settings['testmode'] = $direction;
			update_option( 'woocommerce_stripe_settings', $woocommerce_stripe_settings );

			return 1;

		}

	}

	return 0;
}

// Is feature set?
function th_is_setting_on( $key ) {

	global $th_site_options;

	return is_array( $th_site_options ) && array_key_exists( $key, $th_site_options ) && $th_site_options[ $key ] == 'on';
}


// Stripe to LIVE mode
function th_force_stripe_to_live_mode() {

	if ( th_is_setting_on( 'force_stripe_on' ) ) {

		th_stripe_test_mode_switch_to_( 'no' );
	}
}

// Stripe to TEST mode
function th_stripe_to_test_mode() {

	if ( th_is_setting_on( 'disable_stripe' ) ) {

		th_stripe_test_mode_switch_to_( 'yes' );
	}
}


// Force ACF Pro active
function th_force_acf_pro_active() {

	if ( ! defined( 'WP_PLUGIN_DIR' ) ) {
		return;
	}

	$acf_pro_path = WP_PLUGIN_DIR . '/advanced-custom-fields-pro/acf.php';

	if ( th_is_setting_on( 'force_acf_pro_on' )
	     && file_exists( $acf_pro_path )
	     && ! in_array( 'advanced-custom-fields-pro/acf.php', get_option( 'active_plugins' ) )
	) {

		if ( ! function_exists( 'activate_plugin' ) ) {

			if ( ! defined( 'ABSPATH' ) ) {
				return;
			}

			include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		}

		add_action( 'init', function () {
			activate_plugin( 'advanced-custom-fields-pro/acf.php', '', false, true );
		}, 1 );
	}
}


// Force Allow robots
function th_allow_robots() {

	global $th_site_options;

	if ( ! is_array( $th_site_options )
	     || ! array_key_exists( 'force_robots_on', $th_site_options )
	     || ! $th_site_options['force_robots_on'] ) {

		return;
	}

	if ( ! get_option( 'blog_public' ) ) {
		update_option( 'blog_public', '1' );
	}
}

// Force Disallow robots
function th_disallow_robots() {

	if ( th_is_setting_on( 'prevent_google_bing_indexing' ) ) {
		return;
	}

	global $th_site_options;

	if ( ! is_array( $th_site_options )
	     || ! array_key_exists( 'force_robots_off', $th_site_options )
	     || ! $th_site_options['force_robots_off'] ) {

		return;
	}

	if ( get_option( 'blog_public' ) ) {
		update_option( 'blog_public', '0' );
	}
}


// Prevent Google and Bing indexing while still allowing them to crawl the site.
function th_prevent_search_engine_indexing() {

	if ( ! th_is_setting_on( 'prevent_google_bing_indexing' ) ) {
		return;
	}

	if ( ! get_option( 'blog_public' ) ) {
		update_option( 'blog_public', '1' );
	}

	add_action( 'wp_head', 'th_output_search_engine_noindex_meta_tags', 1 );
}


// Output crawler-specific noindex directives.
function th_output_search_engine_noindex_meta_tags() {

	echo "<meta name='googlebot' content='noindex' />\n";
	echo "<meta name='googlebot-news' content='noindex' />\n";
	echo "<meta name='bingbot' content='noindex' />\n";
}


// Add admin bar notice
add_action( 'admin_bar_menu', 'th_environment_notice', 6 );
function th_environment_notice( $wp_admin_bar ) {

	global $th_dev_environment;
	global $th_environment_title;

	$args = array(
		'id'     => 'dev-or-live',
		'title'  => $th_dev_environment,
		'parent' => 'top-secondary',
		'href'   => '#',
		'meta'   => array(
			'class' => 'dev-or-live',
			'title' => $th_environment_title
		)
	);

	$wp_admin_bar->add_node( $args );
}


function th_on_live_site_css() {

	if ( is_admin_bar_showing() ) {
		wp_enqueue_style(
			'th-live',
			plugins_url( '/css/live-site.css', __FILE__ ),
			[],
			WHICH_ENVIRONMENT_PLUGIN_VERSION
		);
	}
}


function th_in_local_css() {

	if ( is_admin_bar_showing() ) {
		wp_enqueue_style(
			'th-local',
			plugins_url( '/css/local-site.css', __FILE__ ),
			[],
			WHICH_ENVIRONMENT_PLUGIN_VERSION
		);
	}
}


function th_in_devilbox_css() {

	if ( is_admin_bar_showing() ) {
		wp_enqueue_style(
			'th-devilbox',
			plugins_url( '/css/devilbox.css', __FILE__ ),
			[],
			WHICH_ENVIRONMENT_PLUGIN_VERSION
		);
	}
}


function th_on_staging_site_css() {

	if ( is_admin_bar_showing() ) {
		wp_enqueue_style(
			'th-stage',
			plugins_url( '/css/stage-site.css', __FILE__ ),
			[],
			WHICH_ENVIRONMENT_PLUGIN_VERSION
		);
	}
}
