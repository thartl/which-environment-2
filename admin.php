<?php
/*
 * Admin UI for Which Environment
 *
 * Reference:  https://www.sitepoint.com/wordpress-settings-api-build-custom-admin-page/
 *
 */


if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Initialize menus, options, and styles
add_action( 'admin_menu', 'pw_site_options_menu_and_styles', 4200 );
add_action( 'admin_init', 'pw_site_options_init' );


// Add settings page and menu, enqueue styles
function pw_site_options_menu_and_styles() {

	$hook = add_options_page( 'Environment Options',
		'Environment Options',
		'manage_options',
		'pw-site-options',
		'pw_admin_site_options_page'
	);

	add_action( 'admin_print_styles-' . $hook, 'pw_site_options_styles' );

}


// Enqueue admin styles
function pw_site_options_styles() {

	wp_enqueue_style( 'pw_site_options_styles', plugins_url( '/css/pw-admin.css', __FILE__ ) );

}


// Initialize sections and settings
function pw_site_options_init() {

	register_setting( 'pw_site_options', 'pw_site_options' );

	add_settings_section(
		'live-site-domain',
		'',
		'pw_live_site_domain_section',
		'pwSiteOptions'
	);

            add_settings_field(
                'live-site-domain-field',
                'Live site domain',
                'pw_live_domain_field_render',
                'pwSiteOptions',
                'live-site-domain'
            );

    add_settings_section(
        'force-features',
        'Force features on LIVE site',
        'pw_force_features_on_live',
        'pwSiteOptions'
    );

            add_settings_field(
                'force-stripe',
                'Stripe to LIVE mode',
                'pw_force_stripe_field_render',
                'pwSiteOptions',
                'force-features'
            );

            add_settings_field(
                'activate-acf-pro',
                'ACF Pro Active',
                'pw_force_acf_pro_active_field_render',
                'pwSiteOptions',
                'force-features'
            );

            add_settings_field(
                'force_robots_on',
                'Allow robots\' indexing',
                'pw_force_allow_robots_field_render',
                'pwSiteOptions',
                'force-features'
            );

	add_settings_section(
		'disable-features',
		'Disable features if not on LIVE site',
		'pw_not_live_disable_features_section',
		'pwSiteOptions'
	);

            add_settings_field(
                'maybe-Stripe-to-test-mode',
                'Stripe to TEST mode',
	            'pw_disable_stripe_field_render',
                'pwSiteOptions',
                'disable-features'
            );

            add_settings_field(
                'force_robots_off',
                'Disallow robots\' indexing',
                'pw_force_disallow_robots_field_render',
                'pwSiteOptions',
                'disable-features'
            );

            add_settings_field(
                'prevent_google_bing_indexing',
                'Prevent Google and Bing indexing',
                'pw_prevent_google_bing_indexing_field_render',
                'pwSiteOptions',
                'disable-features'
            );

	add_settings_section(
		'site_options_passphrase',
		'',
		'pw_site_options_passphrase_section',
		'pwSiteOptions'
	);

            add_settings_field(
                'site_options_passphrase_field',
                'Passphrase',
                'pw_site_options_passphrase_field_render',
                'pwSiteOptions',
                'site_options_passphrase'
            );

}


/**
 * Sections
 *
 ***********************************************/

function pw_live_site_domain_section() {
    //
}

function pw_force_features_on_live() {
	//
}

function pw_not_live_disable_features_section() {
    //
}

function pw_site_options_passphrase_section() {
	echo '<p>Enter passphrase to confirm changes.  Contact your webmaster or developer for more information.</p>';
}


// Cache options
$th_environment_options = get_option( 'pw_site_options' );


/**
 * Fields
 *
 ***********************************************/

// LIVE domain field
function pw_live_domain_field_render() {

    global $th_environment_options;

	if ( is_array( $th_environment_options ) && array_key_exists( 'site_domain_locked', $th_environment_options ) ) {

		// Get locked domain and remove lock string
		// pw_decipher_locked_domain() is defined in main file
		$unlocked_domain = pw_decipher_locked_domain( $th_environment_options['site_domain_locked'] );

	} else {

		$unlocked_domain = '';

	}

	?>

    <input type='text'
           name='pw_site_options[site_domain_locked]'
           value='<?php echo $unlocked_domain; ?>'
    >

	<?php

}


// Force STRIPE to LIVE mode field
function pw_force_stripe_field_render() {

	global $th_environment_options;

	?>

    <input type="checkbox"
           name="pw_site_options[force_stripe_on]"
        <?php echo
        is_array( $th_environment_options ) && array_key_exists( 'force_stripe_on', $th_environment_options )
            ? 'checked="checked" ' :
            '';
        ?>
    >

	<?php
}

// Force ACF Pro active
function pw_force_acf_pro_active_field_render() {

    global $th_environment_options;

    ?>

    <input type="checkbox"
           name="pw_site_options[force_acf_pro_on]"
		<?php echo
        is_array( $th_environment_options ) && array_key_exists( 'force_acf_pro_on', $th_environment_options )
            ? 'checked="checked" '
            : '';
		?>
    >

    <?php

}

// Force Robots to Allow indexing
function pw_force_allow_robots_field_render() {

	global $th_environment_options;

	?>

    <input type="checkbox"
           name="pw_site_options[force_robots_on]"
		<?php echo
		is_array( $th_environment_options )
        && array_key_exists( 'force_robots_on', $th_environment_options )
        && $th_environment_options['force_robots_on']
			? 'checked="checked" '
			: '';
		?>
    >

	<?php

}

// Deactivate STRIPE field
function pw_disable_stripe_field_render() {

	global $th_environment_options;

	?>

    <input type="checkbox"
           name="pw_site_options[disable_stripe]"
        <?php echo
        is_array( $th_environment_options ) && array_key_exists( 'disable_stripe', $th_environment_options )
            ? 'checked="checked" '
            : '';
        ?>
    >

	<?php

}

// Force Robots to Disallow indexing
function pw_force_disallow_robots_field_render() {

	global $th_environment_options;

	?>

    <input type="checkbox"
           name="pw_site_options[force_robots_off]"
		<?php echo
		is_array( $th_environment_options )
        && array_key_exists( 'force_robots_off', $th_environment_options )
        && $th_environment_options['force_robots_off']
			? 'checked="checked" '
			: '';
		?>
    >

	<?php

}

// Prevent Google and Bing indexing field.
function pw_prevent_google_bing_indexing_field_render() {

	global $th_environment_options;

	?>

    <input type="checkbox"
           name="pw_site_options[prevent_google_bing_indexing]"
		<?php echo
		is_array( $th_environment_options )
		&& array_key_exists( 'prevent_google_bing_indexing', $th_environment_options )
		&& $th_environment_options['prevent_google_bing_indexing']
			? 'checked="checked" '
			: '';
		?>
    >

	<?php

}

// PASSPHRASE field
function pw_site_options_passphrase_field_render() {

	?>

    <input
            type='text'
            name='pw_site_options[passphrase]'
            value=''
    >

	<?php

}


// Print settings page
function pw_admin_site_options_page() {
	?>
    <h1 class="pw-site-options-h1">Environment options</h1>

    <form action='options.php' method='post'>

		<?php
		settings_fields( 'pw_site_options' );

		do_settings_sections( 'pwSiteOptions' );

		submit_button();
		?>

    </form>
	<?php
}


// 1) Check passphrase
// 2) Replace domain string with a locked version
add_filter( 'pre_update_option_pw_site_options', 'pw_check_passphrase_and_lock_site_domain', 10, 2 );
function pw_check_passphrase_and_lock_site_domain( $options, $old_options ) {

    if ( $options['passphrase'] == '4242' ) {

	    $options['site_domain_locked'] = pw_lock_domain( $options['site_domain_locked'] );

	    unset( $options['passphrase'] );

	    return $options;

    }

    $message = 'Incorrect passphrase was used. No changes were saved.';
    $type = 'error';

	add_settings_error('passphrase', 'th_option_error_notice', $message, $type);

	return $old_options;

}


// Lock domain / prevent overwriting during DB search & replace operations
function pw_lock_domain( $domain ) {

	return substr_replace( $domain, '_[pw_site_domain_lock]_', intval( strlen( $domain ) / 2 ), 0 );
}
