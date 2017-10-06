<?php
/**
 * Plugin Name: CMB2 RGBa Colorpicker
 * Plugin URI:  http://plugish.com
 * Description: Adds a RGBa Colorpicker to the CMB2 field types, original JS from 23r9i0 on github.
 * Version:     0.2.0
 * Author:      Jay Wood
 * Author URI:  http://plugish.com
 * Donate link: http://plugish.com
 * License:     GPLv2+
 * Text Domain: jw-cmb2-slider
 */

/**
 * Copyright (c) 2015 Jay Wood (email : jay@plugish.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

if( ! class_exists( 'JW_Fancy_Color' ) ) {

	class JW_Fancy_Color {
		const VERSION = '0.2.0';

		public function hooks() {
			add_action( 'cmb2_render_rgba_colorpicker', array( $this, 'render_color_picker' ), 10, 5 );
			add_action( 'admin_enqueue_scripts', array( $this, 'setup_admin_scripts' ) );
		}

		public function render_color_picker( $field, $field_escaped_value, $field_object_id, $field_object_type, $field_type_object ) {
			echo $field_type_object->input( array(
				'class'              => 'cmb2-colorpicker color-picker',
				'data-default-color' => $field->args( 'default' ),
				'data-alpha'         => 'true',
			) );
		}

		public function setup_admin_scripts() {
			wp_enqueue_style( 'wp-color-picker' );
			wp_enqueue_script( 'jw-cmb2-rgba-picker-js', plugins_url( 'js/jw-cmb2-rgba-picker.js', __FILE__ ), array( 'wp-color-picker' ), self::VERSION, true );
		}
	}

	$jw_fancy_color = new JW_Fancy_Color();
	$jw_fancy_color->hooks();
}

