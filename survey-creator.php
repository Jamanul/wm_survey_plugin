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

// created the custom table in db 
function wm_create_custom_table()
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'survey_table';
    $charset_collate = $wpdb->get_charset_collate();
    $sql = "CREATE TABLE $table_name (
    meta_id mediumint(9) NOT NULL AUTO_INCREMENT,
    post_id mediumint(9) NOT NULL,
    meta_key varchar(255) DEFAULT NULL,
    meta_value longtext,
    PRIMARY KEY (meta_id)
    ) $charset_collate;";
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

register_activation_hook(__FILE__, 'wm_create_custom_table');



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
function cus_title_creation($post)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'survey_table';
    $meta_key = 'title_meta';
    $title_meta = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT meta_value FROM $table_name WHERE post_id = %d AND meta_key = %s",
            $post->ID,
            $meta_key
        )
    );

    //$title_meta = get_post_meta($post->ID, 'title_meta', true);
    ?>
    <label>Survey Title</label>
    <input name="title_meta" value="<?php echo $title_meta; ?>" />
    <?php
}
add_action("admin_init", "wm_cus_meta_box");

// save the title in the post_meta
// function wm_save_details()
// {
//     global $post;
//     update_post_meta($post->ID, "title_meta", $_POST["title_meta"]);
// }

// save the title in the my own meta
function wm_save_details($post_id)
{

    global $wpdb;

    if (isset($_POST['title_meta'])) {
        $title_meta = $_POST['title_meta'];
        $table_name = $wpdb->prefix . 'survey_table';
        $meta_key = 'title_meta';

        // Check if entry already exists
        $existing = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT meta_id FROM $table_name WHERE post_id = %d AND meta_key = %s",
                $post_id,
                $meta_key
            )
        );

        if ($existing === null) {
            // Insert new meta
            $wpdb->insert(
                $table_name,
                array(
                    'post_id' => $post_id,
                    'meta_key' => $meta_key,
                    'meta_value' => $title_meta,
                ),
                array('%d', '%s', '%s')
            );
        } else {
            // Update existing meta
            $wpdb->update(
                $table_name,
                array('meta_value' => $title_meta),
                array(
                    'post_id' => $post_id,
                    'meta_key' => $meta_key,
                ),
                array('%s'),
                array('%d', '%s')
            );
        }
    }
}

add_action('save_post', 'wm_save_details');

// activates the plugin
function wm_plugin_activate()
{
    // need to call it to create the post type
    wm_custom_survey();
}

register_activation_hook(__FILE__, 'wm_plugin_activate');






// deactivate the plugin
function wm_plugin_deactivate()
{

}
register_deactivation_hook(__FILE__, 'wm_plugin_deactivate');

//uninstalls the plugin
function wm_plugin_uninstall()
{
    // i guess it will drop the table when i unsintall the plugin --> have to test it later.
    global $wpdb;

    $table_name = $wpdb->prefix . 'survey_table';

    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}
register_uninstall_hook(__FILE__, 'wm_plugin_uninstall');


