<?php
/*
 * Copyright (c) 2006-2012 Oliver Seidel (email : oliver.seidel @ deliciousdays.com)
 * Copyright (c) 2014-2017 Bastian Germann
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Gutenberg Block Integration for cforms2
 * Provides modern block editor support
 */

/**
 * Register cforms2 Gutenberg block
 */
function cforms2_register_gutenberg_block() {
    // Check if Gutenberg is available
    if (!function_exists('register_block_type')) {
        return;
    }

    // Register block script
    wp_register_script(
        'cforms2-block',
        plugin_dir_url(__FILE__) . 'blocks/cforms-block.js',
        array(
            'wp-blocks',
            'wp-components',
            'wp-data',
            'wp-element',
            'wp-i18n'
        ),
        CFORMS2_VERSION,
        true
    );

    // Localize script with available forms
    $forms = cforms2_get_available_forms_for_block();
    wp_localize_script('cforms2-block', 'cforms2_forms', $forms);

    // Register block type
    register_block_type('cforms2/form', array(
        'editor_script' => 'cforms2-block',
        'render_callback' => 'cforms2_render_block',
        'attributes' => array(
            'formId' => array(
                'type' => 'string',
                'default' => '1'
            ),
            'formName' => array(
                'type' => 'string',
                'default' => ''
            )
        )
    ));
}

/**
 * Get available forms for block editor
 */
function cforms2_get_available_forms_for_block() {
    $forms = array();
    
    try {
        $form_settings = Cforms2\FormSettings::forms();
        
        foreach ($form_settings as $form_id => $form) {
            $id = $form_id === '' ? '1' : $form_id;
            $forms[] = array(
                'id' => $id,
                'name' => $form->name() ?: sprintf(__('Form %s', 'cforms2'), $id)
            );
        }
    } catch (Exception $e) {
        // Fallback if forms can't be loaded
        $forms[] = array(
            'id' => '1',
            'name' => __('Default Form', 'cforms2')
        );
    }
    
    return $forms;
}

/**
 * Render cforms2 block on frontend
 */
function cforms2_render_block($attributes) {
    $form_id = isset($attributes['formId']) ? sanitize_text_field($attributes['formId']) : '1';
    
    // Validate form ID
    if (!cforms2_form_exists($form_id)) {
        return '<div class="cforms2-error">' . 
               sprintf(__('cforms2: Form with ID "%s" not found.', 'cforms2'), esc_html($form_id)) . 
               '</div>';
    }
    
    // Use existing cforms2 shortcode functionality
    return cforms2_shortcode(array('name' => $form_id));
}

/**
 * Check if a form exists
 */
function cforms2_form_exists($form_id) {
    try {
        $form = Cforms2\FormSettings::form($form_id);
        return $form !== null;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Add block category for cforms2
 */
function cforms2_add_block_category($categories, $post) {
    return array_merge(
        $categories,
        array(
            array(
                'slug' => 'cforms2',
                'title' => __('cforms2', 'cforms2'),
                'icon' => 'feedback'
            )
        )
    );
}

/**
 * Initialize Gutenberg integration
 */
function cforms2_init_gutenberg() {
    // Only load in admin and if Gutenberg is available
    if (!is_admin() || !function_exists('register_block_type')) {
        return;
    }
    
    add_action('init', 'cforms2_register_gutenberg_block');
    
    // Add custom block category (WordPress 5.8+)
    if (version_compare(get_bloginfo('version'), '5.8', '>=')) {
        add_filter('block_categories_all', 'cforms2_add_block_category', 10, 2);
    } else {
        add_filter('block_categories', 'cforms2_add_block_category', 10, 2);
    }
}

// Initialize Gutenberg integration
cforms2_init_gutenberg();
