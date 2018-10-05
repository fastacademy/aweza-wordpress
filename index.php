<?php
/**
 * @package aweza-wp
 * @version 1.0
 */
/*
Plugin Name: Aweza for Wordpress
Plugin URI: https://github.com/fastacademy/aweza-wp
Description: This plugin allows content to be augmented with the Aweza Popup.
Author: FastAcademy
Version: 1.0
Author URI: http://fastacademy.co.za/
*/
wp_enqueue_style('aweza-popup', plugins_url('aweza-wp') . '/assets/aweza-popup.min.css');
wp_enqueue_script('aweza-popup', plugins_url('aweza-wp') . '/assets/aweza-popup.min.js');

/**
 * @internal never define functions inside callbacks.
 * these functions could be run multiple times; this would result in a fatal error.
 */

/**
 * custom option and settings
 */
function aweza_settings_init() {
    // register a new setting for "aweza" page
    register_setting( 'aweza', 'aweza_options' );

    // register a new section in the "aweza" page
    add_settings_section(
        'aweza_section_plugin',
        __( 'Plugin Settings', 'aweza' ),
        'aweza_section_plugin_cb',
        'aweza'
    );

    // register a new field in the "aweza_section_plugin" section, inside the "aweza" page
    add_settings_field(
        'aweza_field_key',
        __( 'Key', 'aweza' ),
        'aweza_field_key_cb',
        'aweza',
        'aweza_section_plugin',
        [
            'label_for' => 'aweza_field_key',
            'class' => 'aweza_row',
            'aweza_custom_data' => 'custom',
        ]
    );

    // register a new field in the "aweza_section_plugin" section, inside the "aweza" page
    add_settings_field(
        'aweza_field_secret',
        __( 'Secret', 'aweza' ),
        'aweza_field_secret_cb',
        'aweza',
        'aweza_section_plugin',
        [
            'label_for' => 'aweza_field_secret',
            'class' => 'aweza_row',
            'aweza_custom_data' => 'custom',
        ]
    );
}

/**
 * register our aweza_settings_init to the admin_init action hook
 */
add_action( 'admin_init', 'aweza_settings_init' );


// pill field cb

// field callbacks can accept an $args parameter, which is an array.
// $args is defined at the add_settings_field() function.
// wordpress has magic interaction with the following keys: label_for, class.
// the "label_for" key value is used for the "for" attribute of the <label>.
// the "class" key value is used for the "class" attribute of the <tr> containing the field.
// you can add custom key value pairs to be used inside your callbacks.
function aweza_field_key_cb( $args ) {
    // get the value of the setting we've registered with register_setting()
    $options = get_option( 'aweza_options' );
    // output the field
    ?>
    <input id="<?php echo esc_attr( $args['label_for'] ); ?>" name="aweza_options[<?php echo esc_attr( $args['label_for'] ); ?>]">

    <?php
}

function aweza_field_secret_cb( $args ) {
    // get the value of the setting we've registered with register_setting()
    $options = get_option( 'aweza_options' );
    // output the field
    ?>
    <input type="password" id="<?php echo esc_attr( $args['label_for'] ); ?>" name="aweza_options[<?php echo esc_attr( $args['label_for'] ); ?>]">

    <?php
}

/**
 * top level menu
 */
function aweza_options_page() {
    // add top level menu page
    add_menu_page(
        'Aweza',
        'Aweza Options',
        'manage_options',
        'aweza',
        'aweza_options_page_html'
    );
}

/**
 * register our aweza_options_page to the admin_menu action hook
 */
add_action( 'admin_menu', 'aweza_options_page' );

/**
 * top level menu:
 * callback functions
 */
function aweza_options_page_html() {
    // check user capabilities
    if ( ! current_user_can( 'manage_options' ) ) {
        return;
    }

    // add error/update messages

    // check if the user have submitted the settings
    // wordpress will add the "settings-updated" $_GET parameter to the url
    if ( isset( $_GET['settings-updated'] ) ) {
        // add settings saved message with the class of "updated"
        add_settings_error( 'aweza_messages', 'aweza_message', __( 'Settings Saved', 'aweza' ), 'updated' );
    }

    // show error/update messages
    settings_errors( 'aweza_messages' );
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
        <form action="options.php" method="post">
            <?php
            // output security fields for the registered setting "aweza"
            settings_fields( 'aweza' );
            // output setting sections and their fields
            // (sections are registered for "aweza", each field is registered to a specific section)
            do_settings_sections( 'aweza' );
            // output save settings button
            submit_button( 'Save Settings' );
            ?>
        </form>
    </div>
    <?php
}