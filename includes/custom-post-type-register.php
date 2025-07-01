<?php
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