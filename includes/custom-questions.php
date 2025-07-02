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
        $wpdb->prepare("SELECT * FROM $table_name WHERE post_id = %d AND meta_key =%s", $post->ID, 'survey_question')
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
                    <input type="text" name="survey_questions[<?php echo $row->meta_id; ?>][question]"
                        value="<?php echo esc_attr($row->meta_value); ?>" />
                </div>
                <div>
                    <label>Answer</label>
                    <select name="survey_questions[<?php echo $row->meta_id; ?>][answer]">
                        <option value="1" <?php selected($row->question_answer, '1'); ?>>True</option>
                        <option value="0" <?php selected($row->question_answer, '0'); ?>>False</option>
                    </select>
                </div>
                <div>
                    <button class='delete-button' data-question-id="<?php echo $row->meta_id; ?>">delete</button>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <button type="button" id="add-question-btn" class="button">+ Add New Question</button>

    <script>
        // to delete a row function
        document.addEventListener('DOMContentLoaded', () => {
            // DELETE LOGIC
            document.querySelectorAll(".delete-button").forEach(deleteBtn => {
                deleteBtn.addEventListener("click", (e) => {
                    e.preventDefault();
                    const formData = new FormData();
                    formData.append('action', 'wm_delete_a_row');
                    formData.append('meta_id', deleteBtn.getAttribute('data-question-id'));

                    fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
                        method: "POST",
                        body: formData
                    })
                        .then(res => res.json())
                        .then(data => alert(`${data.message}`))
                });
            });

            // ADD NEW LOGIC
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
            <div>
                <button type="button" class='delete-button' disabled>Delete</button>
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

    if (!isset($_POST['survey_questions']) || !is_array($_POST['survey_questions'])) {
        return;
    }

    foreach ($_POST['survey_questions'] as $meta_id => $data) {
        $question_value = $data['question'] ?? '';
        $answer_value = $data['answer'] ?? '';

        // If $meta_id is numeric → update
        if (is_numeric($meta_id)) {
            $existing = $wpdb->get_var($wpdb->prepare(
                "SELECT meta_id FROM $table_name WHERE meta_id = %d AND post_id = %d",
                $meta_id,
                $post_id
            ));

            if ($existing !== null) {
                $wpdb->update(
                    $table_name,
                    [
                        'meta_value' => $question_value,
                        'question_answer' => $answer_value
                    ],
                    ['meta_id' => $meta_id, "meta_key" => "survey_question"],
                    ['%s', '%s'],
                    ['%d', "%s"]
                );
            }

        } else {
            // Treat as a new insert
            $wpdb->insert(
                $table_name,
                [
                    'post_id' => $post_id,
                    'meta_key' => "survey_question",
                    'meta_value' => $question_value,
                    'question_answer' => $answer_value
                ],
                ['%d', '%s', '%s', '%s']
            );
        }
    }
}

add_action('save_post', 'wm_save_question');

// to delete the row
add_action('wp_ajax_wm_delete_a_row', 'wm_delete_a_row');

function wm_delete_a_row()
{
    $meta_id = $_POST['meta_id'];
    global $wpdb;
    $table = $wpdb->prefix . 'survey_table';
    $wpdb->delete($table, ['meta_id' => $meta_id]);
    echo json_encode(['message' => 'deleted']);
    wp_die();
}

