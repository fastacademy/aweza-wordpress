<?php
/**
 * @package aweza-wordpress
 * @version 1.0
 */

/*
Plugin Name: Aweza
Plugin URI: https://github.com/fastacademy/aweza-wordpress
Description: The official Aweza plugin for WordPress.
Author: FastAcademy
Version: 1.0
Author URI: http://fastacademy.co.za/
*/
define('AWEZA_PLUGIN_NAME', 'aweza-wordpress');
define('AWEZA_POPUP_VERSION', '3.0.2');

function enqueue_aweza_scripts() {
    wp_enqueue_style(
        'aweza-popup',
        plugins_url(AWEZA_PLUGIN_NAME) . '/assets/aweza-popup.min.css',
        [],
        AWEZA_POPUP_VERSION
    );
    wp_enqueue_script(
        'aweza-popup',
        plugins_url(AWEZA_PLUGIN_NAME) . '/assets/aweza-popup.min.js',
        [],
        AWEZA_POPUP_VERSION
    );

    $aweza_api_key = json_encode(get_option('aweza_options')['aweza_key']);
    $aweza_api_secret = json_encode(get_option('aweza_options')['aweza_secret']);
    $aweza_prefer_lang = json_encode(get_option('aweza_options')['aweza_prefer_lang']);

    $aweza_popup_init = <<<SCRIPT
window.addEventListener('load', function() {
  window.AwezaPopup({
    headers: {
      'AWEZA-KEY': $aweza_api_key,
      'AWEZA-SECRET': $aweza_api_secret
    },
    preferLang: $aweza_prefer_lang
  })
})
SCRIPT;

    wp_add_inline_script('aweza-popup-init', $aweza_popup_init);
}

add_action('wp_enqueue_scripts', 'enqueue_aweza_scripts');

class Aweza_Settings
{
    private static $page = 'aweza';
    private static $option_group = 'aweza';
    private static $option_name = 'aweza_options';

    public static function register()
    {
        register_setting(self::$option_group, self::$option_name);

        // Auth Section
        $section_slug = 'aweza_auth';
        add_settings_section(
            $section_slug,
            'Authentication',
            [self::class, 'render_auth_section_heading'],
            self::$page
        );

        self::add_setting_field(
            'aweza_key',
            'Key',
            [self::class, 'render_key_field'],
            $section_slug
        );

        self::add_setting_field(
            'aweza_secret',
            'Secret',
            [self::class, 'render_secret_field'],
            $section_slug
        );

        // Popup section
        $section_slug = 'aweza_popup';
        add_settings_section($section_slug,
            'Popup',
            [self::class, 'render_popup_section_heading'],
            self::$page
        );

        self::add_setting_field(
            'aweza_prefer_lang',
            'Prefer Lang',
            [self::class, 'render_prefer_lang_field'],
            $section_slug
        );

    }

    public static function render_options_page()
    {
        ?>
      <div class="wrap">
        <h1><?= esc_html(get_admin_page_title()); ?></h1>
        <form action="options.php" method="post">
            <?php
            settings_fields(self::$option_group);
            do_settings_sections(self::$page);
            submit_button('Save Settings');
            ?>
        </form>
      </div>
        <?php
    }

    public static function render_popup_section_heading()
    {
        echo 'Customise the Popup\'s behaviour.';
    }

    public static function render_auth_section_heading()
    {
        echo 'Enter your Aweza API credentials here.';
    }

    public static function render_key_field($args)
    {
        $key = get_option(self::$option_name)['aweza_key'];
        ?>
      <input id="<?= esc_attr($args['label_for']); ?>"
             name="aweza_options[<?= esc_attr($args['label_for']); ?>]"
             value="<?= $key ?>">
        <?php
    }

    public static function render_secret_field($args)
    {
        $secret = get_option(self::$option_name)['aweza_secret'];
        ?>
      <input id="<?= esc_attr($args['label_for']); ?>"
             name="aweza_options[<?= esc_attr($args['label_for']); ?>]"
             value="<?= $secret ?>">
        <?php
    }

    public static function render_prefer_lang_field($args)
    {
        $prefer_lang = get_option(self::$option_name)['aweza_prefer_lang'];
        ?>
      <input id="<?= esc_attr($args['label_for']); ?>"
             name="aweza_options[<?= esc_attr($args['label_for']); ?>]"
             value="<?= $prefer_lang ?>">
        <?php
    }

    private static function add_setting_field($slug, $title, $renderer, $section_slug)
    {
        add_settings_field(
            $slug,
            $title,
            $renderer,
            self::$page,
            $section_slug,
            ['label_for' => $slug]
        );
    }
}
add_action('admin_init', ['Aweza_Settings', 'register']);

class Aweza_Menus
{
    private static $page_title = 'Aweza Settings';
    private static $menu_title = 'Aweza';
    private static $capability = 'manage_options';
    private static $menu_slug = 'aweza';
    private static $render_cb = ['Aweza_Settings', 'render_options_page'];

    public static function add_options_page()
    {
        add_options_page(
            self::$page_title,
            self::$menu_title,
            self::$capability,
            self::$menu_slug,
            self::$render_cb
        );
    }


}

add_action('admin_menu', ['Aweza_Menus', 'add_options_page']);