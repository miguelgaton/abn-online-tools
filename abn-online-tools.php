<?php
/*
	Plugin Name: ABN Online Tools
	Plugin URI:  http://www.actualidadblog.com/
	Description: Online Tools for the awesome ABN Framework theme
	Version:     1.2
	Author:      Raul Illana <raul.illana@abinternet.es>
	Author URI:  http://raulillana.com/
	License:     GPL2

	GitHub Plugin URI: https://github.com/ABInternet/abn-online-tools/
	GitHub Branch:     master
*/
// If this file is called directly, abort.
if( !defined('WPINC') )
	die;

// hooks
register_activation_hook(__FILE__,   array('ABN_Online_Tools', '_activate'));
register_deactivation_hook(__FILE__, array('ABN_Online_Tools', '_deactivate'));
register_uninstall_hook(__FILE__,    array('ABN_Online_Tools', '_uninstall'));

// shortcodes
add_shortcode('abnot-parent', array('ABN_Online_Tools', 'sct_parent'));
add_shortcode('abnot-string-length', array('ABN_Online_Tools', 'sct_string_length'));
add_shortcode('abnot-word-count', array('ABN_Online_Tools', 'sct_word_count'));
add_shortcode('abnot-color-picker', array('ABN_Online_Tools', 'sct_color_picker'));
add_shortcode('abnot-hex-rgb', array('ABN_Online_Tools', 'sct_hex_rgb'));
add_shortcode('abnot-rgb-hex', array('ABN_Online_Tools', 'sct_rgb_hex'));

add_shortcode('abnot-rgb-cmyk', array('ABN_Online_Tools', 'sct_rgb_cmyk'));
add_shortcode('abnot-cmyk-rgb', array('ABN_Online_Tools', 'sct_cmyk_rgb'));

// actions
add_action('wp_enqueue_scripts', array('ABN_Online_Tools', 'load_scripts'));

/**/
class ABN_Online_Tools
{
	// ...
	static $tools = array(
			'string-length' => array(
				'title'     => 'Contar caracteres',
				'shortcode' => '[abnot-string-length]'
			),
			'word-count'    => array(
				'title'     => 'Contar palabras',
				'shortcode' => '[abnot-word-count]'
			),
			'color-picker'  => array(
				'title'     => 'Seleccionar color',
				'shortcode' => '[abnot-color-picker]'
			),
			'hex-rgb'       => array(
				'title'     => 'Convertir color HEX a RGB',
				'shortcode' => '[abnot-hex-rgb]'
			),
			'rgb-hex'       => array(
				'title'     => 'Convertir color RGB a HEX',
				'shortcode' => '[abnot-rgb-hex]'
			),
			'rgb-cmyk'       => array(
				'title'     => 'Convertir color RGB a CMYK',
				'shortcode' => '[abnot-rgb-cmyk]'
			),
			'cmyk-rgb'       => array(
				'title'     => 'Convertir color CMYK a RGB',
				'shortcode' => '[abnot-cmyk-rgb]'
			)
		);

	// construct
	public static function _construct()
	{

	}

	// scripts
	public static function load_scripts()
	{
		foreach( self::$tools as $tool => $t )
		{
			if( is_page($t['title']) )
			{
				wp_enqueue_style( 'abn-color-picker-css', plugins_url( 'wp-color-picker.min.css', __FILE__ ) );
				wp_enqueue_script( 'jquery' );
				wp_enqueue_script( 'iris', plugins_url( 'iris.min.js', __FILE__ ), array( 'jquery', 'jquery-ui-draggable', 'jquery-ui-slider', 'jquery-touch-punch' ), false, 1 );
				wp_enqueue_script( 'wp-color-picker', plugins_url( 'color-picker.min.js', __FILE__ ), array('iris'), false, 1 );

				$colorpicker_l10n = array(
					'clear'         => __('Clear'),
					'defaultString' => __('Default'),
					'pick'          => __('Select Color')
				);

				wp_localize_script('wp-color-picker', 'wpColorPickerL10n', $colorpicker_l10n);

				wp_enqueue_style('abn-online-tools-css', plugins_url('abn-online-tools.css', __FILE__));
				wp_enqueue_script('abn-online-tools-js', plugins_url('abn-online-tools.js', __FILE__), array('iris'), false, true);
			}
		}
	}

	// add pages on activate
	public static function _activate()
	{
		$parent_id = self::create_parent();

		if( !$parent_id || is_wp_error($parent_id) || empty($parent_id) )
			error_log('ABNOT@activate: Parent create error!');

		else foreach( self::$tools as $tool => $t )
		{
			$pid = get_page_by_title($t['title'], 'OBJECT', 'page');

			if( !$pid || is_wp_error($pid) )
				self::create_page($t['title'], $t['shortcode'], $parent_id);

			else wp_update_post(array('ID' => $pid->ID, 'post_status' => 'publish'));
		}
	}

	// draft pages on deactivate
	public static function _deactivate()
	{
		if( empty(self::$page_ids) )
			error_log('ABNOT@deactivate: Empty $page_ids!');

		foreach( self::$tools as $tool => $t )
		{
			$pid = get_page_by_title($t['title'], 'OBJECT', 'page');
			wp_update_post(array('ID' => $pid->ID, 'post_status' => 'draft'));
		}
	}

	// delete pages on uninstall
	public static function _uninstall()
	{
		if( empty(self::$page_ids) )
			error_log('ABNOT@uninstall: Empty $page_ids!');

		foreach( self::$tools as $tool => $t )
		{
			$pid = get_page_by_title($t['title'], 'OBJECT', 'page');
			wp_delete_post($pid->ID, true); // forced
		}
	}

	// create parent page
	public static function create_parent()
	{
		$is = get_page_by_title('Herramientas Online', 'OBJECT', 'page');

		if( !$is || is_wp_error($is) )
		{
			$parent = array(
				'post_title'   => __('Herramientas Online', 'abnot'),
				'post_content' => '[abnot-parent]',
				'post_status'  => 'publish',
				'post_type'    => 'page'
			);

			$post_id = wp_insert_post($parent);

			return $post_id;
		}
		else return $is->ID;
	}

	// create child pages
	public static function create_page($title, $shortcode, $parent_id)
	{
		$is = get_page_by_title($title, 'OBJECT', 'page');

		if( !$is || is_wp_error($is) )
		{
			$page = array(
				'post_title'   => $title,
				'post_content' => $shortcode,
				'post_parent'  => $parent_id,
				'post_status'  => 'publish',
				'post_type'    => 'page'
			);

			$post_id = wp_insert_post($page);

			return $post_id;
		}
		else return $is->ID;
	}

	/***/

	//
	public static function sct_parent()
	{
		global $post;

		// print childs
		$pages = get_pages('child_of='. $post->ID .'&sort_column=post_name&sort_order=desc');
		$count = 0;
		$res   = '<ul>';

		foreach( $pages as $page )
			$res .= '<li><a href="'. get_page_link($page->ID) .'">'. $page->post_title .'</a></li>';

		$res .= '</ul>';

		return $res;
	}

	//
	public static function sct_string_length()
	{
		ob_start();
?>
<div class="abnot">
	<textarea id="sct_string_length_ta" name="sct_string_length_ta"></textarea>
	<input class="xlarge abn awebsome" type="submit" id="sct_string_length_sub" name="sct_string_length_sub" value="<?php _e('Contar caracteres', 'abnot') ?>"> <span id="restool"></span>
</div>
<?php
		return ob_get_clean();
	}

	//
	public static function sct_word_count()
	{
		ob_start();
?>
<div class="abnot">
	<textarea id="sct_word_count_ta" name="sct_word_count_ta"></textarea>
	<input class="xlarge abn awebsome" type="submit" id="sct_word_count_sub" name="sct_word_count_sub" value="<?php _e('Contar palabras', 'abnot') ?>"> <span id="restool"></span>
</div>
<?php
		return ob_get_clean();
	}

	//
	public static function sct_color_picker()
	{
		ob_start();
?>
<div class="abnot">
	<input class="color-picker" type="text" id="sct_color_picker_i" name="sct_color_picker_i"> <span id="restool"></span>
</div>
<?php
		return ob_get_clean();
	}

	//
	public static function sct_rgb_hex()
	{
		ob_start();
?>
<div class="abnot">
	<p><input type="text" id="sct_rgb_hex_i" name="sct_rgb_hex_i" placeholder="Ej: 250,188,24"></p>
	<input class="xlarge abn awebsome" type="submit" id="sct_rgb_hex_sub" name="sct_rgb_hex_sub" value="<?php _e('Convertir a HEX', 'abnot') ?>"> <span id="restool"></span>
</div>
<?php
		return ob_get_clean();
	}

	//
	public static function sct_hex_rgb()
	{
		ob_start();
?>
<div class="abnot">
	<p><input type="text" id="sct_hex_rgb_i" name="sct_hex_rgb_i" placeholder="Ej: FF00AA"></p>
	<input class="xlarge abn awebsome" type="submit" id="sct_hex_rgb_sub" name="sct_hex_rgb_sub" value="<?php _e('Convertir a RGB', 'abnot') ?>"> <span id="restool"></span>
</div>
<?php
		return ob_get_clean();
	}

	//
	public static function sct_rgb_cmyk()
	{
		ob_start();
?>
<div class="abnot">
	<p><input type="text" id="sct_rgb_cmyk_i" name="sct_rgb_cmyk_i" placeholder="Ej: 250,188,24"></p>
	<input class="xlarge abn awebsome" type="submit" id="sct_rgb_cmyk_sub" name="sct_rgb_cmyk_sub" value="<?php _e('Convertir a CMYK', 'abnot') ?>"> <span id="restool"></span>
</div>
<?php
		return ob_get_clean();
	}

	//
	public static function sct_cmyk_rgb()
	{
		ob_start();
?>
<div class="abnot">
	<p><input type="text" id="sct_cmyk_rgb_i" name="sct_cmyk_rgb_i" placeholder="Ej: 0,0.906,0.729,0"></p>
	<input class="xlarge abn awebsome" type="submit" id="sct_cmyk_rgb_sub" name="sct_cmyk_rgb_sub" value="<?php _e('Convertir a RGB', 'abnot') ?>"> <span id="restool"></span>
</div>
<?php
		return ob_get_clean();
	}
}

new ABN_Online_Tools;
?>