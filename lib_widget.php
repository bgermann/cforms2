<?php
/*
 * Copyright (c) 2012 Oliver Seidel (email : oliver.seidel @ deliciousdays.com)
 * Copyright (c) 2014 Bastian Germann
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
class cforms2_widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'cforms',
			__('cformsII', 'cforms2'),
			array(
				'description' => __('Add any cforms form to your sidebar', 'cforms2')
			),
			array('width' => 200, 'height' => 200)
		);
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		$no = ($instance['form']=='1')?'':$instance['form'];

		echo $args['before_widget'];
		if ( ! empty( $instance['title'] ) )
			echo $args['before_title'].apply_filters( 'widget_title', $instance['title'] ).$args['after_title'];
		insert_cform($no);
		echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {
		
		$cformsSettings = get_option('cforms_settings');

		// stored data
		$title = isset($instance['title']) ? $instance['title'] : '';
		$form = isset($instance['form']) ? $instance['form'] : null;


		$opt = '';
		$forms = $cformsSettings['global']['cforms_formcount'];
		for ($i=1;$i<=$forms;$i++) {
			$no = ($i==1)?'':($i);
			$selected = ( $i==$form )? ' selected="selected"':'';
			$name = stripslashes($cformsSettings['form'.$no]['cforms'.$no.'_fname']);
			$name = (strlen($name)>40) ? substr($name,0,40).'&#133':$name;
			$opt .= '<option value="'.$i.'"'. $selected .'>'.$name.'</option>';
		}

	echo '<label for="'.$this->get_field_id( 'title' ).'">' . __('Title', 'cforms2') . ':</label>'.
		 '<input type="text" id="' .$this->get_field_id( 'title' ). '" name="' .$this->get_field_name( 'title' ). '" value="' .esc_attr($title). '" /><br />';

	echo '<label for="'.$this->get_field_id( 'form' ).'">' . __('Form', 'cforms2') . ':</label>'.
		 '<select id="' .$this->get_field_id( 'form' ). '" name="' .$this->get_field_name( 'form' ). '" style="width:220px; font-size:10px; font-family:Arial;">'. $opt .'</select>';
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 */
	public function update( $new_instance, $old_instance ) {
		$check = array('title', 'form');
		$instance = array();
		foreach ( $check as $value ) {
			if (empty( $new_instance[$value] )) {
				$instance[$value] = '';
			} else {
				$instance[$value] = strip_tags( $new_instance[$value] );
			}
		}

		return $instance;
	}
}
