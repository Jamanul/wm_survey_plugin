<?php
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
    // i guess it will drop the table when i delete the plugin --> have to test it later.
    global $wpdb;

    $table_name = $wpdb->prefix . 'survey_table';

    $wpdb->query("DROP TABLE IF EXISTS $table_name");
}
register_uninstall_hook(__FILE__, 'wm_plugin_uninstall');