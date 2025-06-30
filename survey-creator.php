<?php
/*
 * Plugin Name:       Survey Creator
 * Plugin URI:        #
 * Description:       It can create multiple survey with a unique shortcode.It will also recieve user's answer.
 * Version:           1.0.0
 * Requires at least: 5.2
 * Requires PHP:      7.2
 * Author:            Webermelon
 * Author URI:        #
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Update URI:        https://example.com/my-plugin/
 * Text Domain:       webermelon
 * Domain Path:       /languages
 * Requires Plugins:  Survey Creator
 */


//  activate the plugin
function wm_custom_survey()
{
    register_post_type(
        'wm_survey',
        array(
            'labels' => array(
                'name' => __('wm_surveys', 'webermelon'),
                'singular_name' => __('wm_survey', 'webermelon'),
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'survey'), // my custom slug
        )
    );
}
add_action('init', 'wm_custom_survey');
function my_plugin_activate()
{
    wm_custom_survey();

}

register_activation_hook(__FILE__, 'my_plugin_activate');

// deactivate the plugin
function my_plugin_deactivate()
{

}
register_deactivation_hook(__FILE__, 'my_plugin_deactivate');

//uninstalls the plugin


function my_plugin_uninstall()
{
    error_log('Plugin was uninstalled.');
}
register_uninstall_hook(__FILE__, 'my_plugin_uninstall');


