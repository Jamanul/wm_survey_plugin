<?php

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

// save the title in the my own db table
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
                ), //data entry
                array('%d', '%s', '%s')  // format
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