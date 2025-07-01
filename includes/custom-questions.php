<?php

function wm_cus_question_field()
{
    add_meta_box("survey_question", "Survey Question", "cus_question_creation", "wm_survey", "normal", "low");
}

function cus_question_creation($post)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'survey_table';
    $meta_key = "survey_question";
    $question_value = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT meta_value FROM $table_name WHERE post_id=%d AND meta_key=%s ",
            $post->ID,
            $meta_key
        )
    );
    $answer_value = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT question_answer FROM $table_name WHERE post_id=%d AND meta_key=%s ",
            $post->ID,
            $meta_key
        )
    );
    // $question_meta = get_post_meta($post->ID, 'title_meta', true);
    ?>
    <div style="display:flex; align-items: center; gap:48px;">
        <div>
            <label>Survey question</label>
            <input name="survey_question" value="<?php echo $question_value; ?>" />
        </div>
        <div>
            <label>Answer</label>
            <select name="survey_answer">
                <option value="1" <?php selected($answer_value, '1'); ?>>True</option>
                <option value="0" <?php selected($answer_value, '0'); ?>>False</option>
            </select>
        </div>
    </div>
    <?php
}

add_action("admin_init", "wm_cus_question_field");

function wm_save_question($post_id)
{

    global $wpdb;

    if (isset($_POST['survey_question']) && isset($_POST['survey_answer'])) {
        $question_value = $_POST['survey_question'];
        $answer_value = $_POST['survey_answer'];
        $table_name = $wpdb->prefix . 'survey_table';
        $meta_key = 'survey_question';

        // Check if entry already exists
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT meta_id FROM $table_name WHERE post_id = %d AND meta_key = %s",
            $post_id,
            $meta_key
        ));

        if ($existing === null) {
            // Insert new row
            $wpdb->insert(
                $table_name,
                [
                    'post_id' => $post_id,
                    'meta_key' => $meta_key,
                    'meta_value' => $question_value,
                    'question_answer' => $answer_value
                ],
                ['%d', '%s', '%s', '%s']
            );
        } else {
            // Update existing row
            $wpdb->update(
                $table_name,
                [
                    'meta_value' => $question_value,
                    'question_answer' => $answer_value
                ],
                [
                    'post_id' => $post_id,
                    'meta_key' => $meta_key
                ],
                ['%s', '%s'],
                ['%d', '%s']
            );
        }
    }
}



add_action('save_post', 'wm_save_question');