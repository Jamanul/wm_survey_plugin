<?php

function wm_cus_question_field()
{
    add_meta_box("survey_question", "Survey Question", "cus_question_creation", "wm_survey", "normal", "low");
}

function cus_question_creation($post)
{
    global $wpdb;
    $table_name = $wpdb->prefix . 'survey_table';

    // Fetch all question rows for this post
    $questions = $wpdb->get_results(
        $wpdb->prepare("SELECT * FROM $table_name WHERE post_id = %d AND meta_key LIKE %s", $post->ID, 'survey_question_%')
    );


    if (empty($questions)) {
        echo '<div style="margin-bottom:24px;">please click "add new question" button to add question</div>';
    }

    ?>
    <div id="question-fields">
        <?php foreach ($questions as $index => $row): ?>
            <div class="question-block" style="display:flex; align-items:center; gap:48px; margin-bottom:16px;">
                <div>
                    <label>Survey Question</label>
                    <input type="text" name="survey_questions[<?php echo $row->meta_key; ?>][question]"
                        value="<?php $row->meta_value; ?>" />
                </div>
                <div>
                    <label>Answer</label>
                    <select name="survey_questions[<?php echo $row->meta_key; ?>][answer]">
                        <option value="1" <?php selected($row->question_answer, '1'); ?>>True</option>
                        <option value="0" <?php selected($row->question_answer, '0'); ?>>False</option>
                    </select>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <button type="button" id="add-question-btn" class="button">+ Add New Question</button>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            let counter = <?php echo count($questions) + 1; ?>;
            document.getElementById('add-question-btn').addEventListener('click', () => {
                const container = document.getElementById('question-fields');
                const block = document.createElement('div');
                block.className = 'question-block';
                block.style = "display:flex; align-items:center; gap:48px; margin-bottom:16px;";
                block.innerHTML = `
                <div>
                    <label>Survey Question</label>
                    <input type="text" name="survey_questions[survey_question_${counter}][question]" value="" />
                </div>
                <div>
                    <label>Answer</label>
                    <select name="survey_questions[survey_question_${counter}][answer]">
                        <option value="1">True</option>
                        <option value="0">False</option>
                    </select>
                </div>
            `;
                container.appendChild(block);
                counter++;
            });
        });
    </script>
    <?php
}

add_action('admin_init', 'wm_cus_question_field');

function wm_save_question($post_id)
{
    global $wpdb;


    $table_name = $wpdb->prefix . 'survey_table';

    foreach ($_POST['survey_questions'] as $meta_key => $data) {
        $question_value = $data['question'] ?? '';
        $answer_value = $data['answer'] ?? '';

        // Check if row exists
        $existing = $wpdb->get_var($wpdb->prepare(
            "SELECT meta_id FROM $table_name WHERE post_id = %d AND meta_key = %s",
            $post_id,
            $meta_key
        ));

        if ($existing === null) {
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





