<?php
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
    question_answer boolean,
    PRIMARY KEY (meta_id)
    ) $charset_collate;";
    require_once ABSPATH . 'wp-admin/includes/upgrade.php';
    dbDelta($sql);
}

register_activation_hook(__FILE__, 'wm_create_custom_table');