<?php
/*
 * Copyright (c) 2026 Bastian Germann
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

/** Registers the cforms2/form Gutenberg block using PHP-only block registration (requires WordPress 7.0+). */
function cforms2_register_block() {
    $forms = Cforms2\FormSettings::forms();
    $form_names = array();
    foreach ( $forms as $form ) {
        $name = stripslashes( $form->name() );
        if ( $name !== '' ) {
            $form_names[] = $name;
        }
    }

    if ( empty( $form_names ) ) {
        return;
    }

    register_block_type(
        'cforms2/form',
        array(
            'title'           => __( 'cformsII Form', 'cforms2' ),
            'attributes'      => array(
                'form_name' => array(
                    'label'   => __( 'Form', 'cforms2' ),
                    'type'    => 'string',
                    'enum'    => $form_names,
                    'default' => $form_names[0],
                ),
            ),
            'render_callback' => function ( $attributes ) {
                if ( empty( $attributes['form_name'] ) ) {
                    return '';
                }
                return cforms2( cforms2_check_form_name( $attributes['form_name'] ) );
            },
            'supports'        => array(
                'autoRegister' => true,
            ),
        )
    );
}

add_action( 'init', 'cforms2_register_block' );
