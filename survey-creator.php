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

//  funtion to create post type 
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
            'rewrite' => array('slug' => 'survey'), // to create my own slug
        )
    );
}
// registered post type
add_action('init', 'wm_custom_survey');
// custom meta box
function wm_cus_meta_box()
{
    add_meta_box("title_meta", "Survey Title", "cus_title_creation", "wm_survey", "normal", "low");
}
// callback function to create the title 
function cus_title_creation()
{
    global $post;
    $title_meta = get_post_meta($post->ID, 'title_meta', true);
    ?>
    <label>Survey Title</label>
    <input name="title_meta" value="<?php echo $title_meta; ?>" />
    <?php
}
add_action("admin_init", "wm_cus_meta_box");

// save the title in the post
function save_details()
{
    global $post;
    update_post_meta($post->ID, "title_meta", $_POST["title_meta"]);
}
add_action('save_post', 'save_details');

// activates the plugin
function my_plugin_activate()
{
    // need to call it to create the post type
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


