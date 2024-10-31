<?php
/*
Plugin Name: Queerify
Plugin URI: https://github.com/Imoptimal/queerify
Description: Queerify your website by setting a fabulous loading screen that features a choosen flag from the LGBTIQ+ spectrum - representing your own gender identity. If you're a cat lover, you'll be glad to hear that neon cat also makes an appearance. If you fall outside of the LGBTIQ+ spectrum, don't worry - you can choose the option 'I'm a star!', and still sparkle some joy to your website visitors.
Author: Ivan Maljukanović
Author URI: https://imoptimal.com
Version: 1.0.7
Author: imoptimal
Author URI: https://www.imoptimal.com/
Requires at least: 4.9.8
Requires PHP: 5.6
License: GNU General Public License v3 or later
License URI: https://www.gnu.org/licenses/gpl-3.0.en.html
Text Domain: queerify
Domain Path: /lang
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define global constants
if (!defined('QUEERIFY_VERSION')) {
    define('QUEERIFY_VERSION', '1.0.7');
}

if (!defined('QUEERIFY_NAME')) {
    define('QUEERIFY_NAME', trim(dirname(plugin_basename( __FILE__ )), '/' ));
}

if (!defined('QUEERIFY_DIR')) {
    define('QUEERIFY_DIR', WP_PLUGIN_DIR . '/' . QUEERIFY_NAME);
}

if (!defined('QUEERIFY_URL')) {
    define('QUEERIFY_URL', WP_PLUGIN_URL . '/' . QUEERIFY_NAME);
}

// Initialize the plugins Settings-API
require_once QUEERIFY_DIR . '/class-queerify.php';

// Actions/Filters
if (class_exists('Queerify')) {
    // Object Instantiation
    $queerify_object = new Queerify();
    
    // Section: Basic Settings
    $queerify_object->add_section(
        array(
            'id'    => 'queerify_basic',
            'title' => esc_html__('Basic Options', 'queerify'),
        )
    );
    // Field: Minification (checkbox)
	$queerify_object->add_field(
		'queerify_basic',
		array(
			'id'   => 'minification',
			'type' => 'checkbox',
			'name' => esc_html__('Minification', 'queerify'),
			'desc' => esc_html__('Check this box if you want to minify plugins files (sripts and styles)', 'queerify'),
            'std' => 'off',
		)
	);
    
    // Field: Flag selection (select)
    $queerify_object->add_field(
        'queerify_basic',
        array(
            'id'      => 'flags',
            'type'    => 'select',
            'name'    => esc_html__('Gender Identity', 'queerify'),
            'desc'    => esc_html__('Choose your gender identity, that will be reflected in coresponding flag within the loading screen (preview of the flag - to the right)', 'queerify'),
            'options' => array(
                'gay' => esc_html__('Gay (default)', 'queerify'),
                'lesbian' => esc_html__('Lesbian', 'queerify'),
                'bisexual' => esc_html__('Bisexual', 'queerify' ),
                'transgender' => esc_html__('Transgender', 'queerify'),
                'intersex' => esc_html__('Intersex', 'queerify'),
                'genderqueer' => esc_html__('Genderqueer', 'queerify'),
                'nonbinary' => esc_html__('Non-binary', 'queerify'),
                'agender' => esc_html__('Agender', 'queerify'),
                'asexual' => esc_html__('Asexual', 'queerify'),
                'genderfluid' => esc_html__('Genderfluid', 'queerify'),
                'pansexual' => esc_html__('Pansexual', 'queerify'),
                'stars' => esc_html__("I'm a star!", 'queerify'),
            ),
            'std' => 'gay',
        )
    );
    
}
?>
