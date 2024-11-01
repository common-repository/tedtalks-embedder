<?php
/*
* Plugin Name: TEDTalks Embedder
* Plugin URI: http://www.samuelaguilera.com
* Description: Embeds TEDTalks videos on your self hosted WordPress simply using same shortcode used for WordPress.com
* Version: 2.1
* Text Domain: tedtalks
* Author: Samuel Aguilera
* Author URI: http://www.samuelaguilera.com
* License: GPL3
*/

// Check for activated Jetpack, and deactivate TEDTalks Embedder in that case.

function tte_check_jetpack_shortcodes_module() {

  $tte_plugin = plugin_basename( __FILE__ );
 
  if ( class_exists('Jetpack', false ) ) {

      if (Jetpack::init()->is_module_active( 'shortcodes' )) {

	      deactivate_plugins( $tte_plugin );

	      function tte_jetpack_found() {
	          ?>
	          <div class="error">
	              <p><?php esc_html_e( "TEDTalks Embedder can't be used with Jetpack's shortcodes module activated. TEDTalks Embedder has been deactivated!.", 'tedtalks' ); ?></p>
	          </div>
	          <?php
	      }
	      add_action( 'admin_notices', 'tte_jetpack_found' );

  	}
  }
}

add_action( 'admin_init', 'tte_check_jetpack_shortcodes_module' );

//Load translation file if any for the current language
load_plugin_textdomain('tedtalks', false, dirname( plugin_basename( __FILE__ ) ) . '/locale');

// Settings page

add_action( 'admin_init', 'tte_settings_init' );
function tte_settings_init() {
    register_setting( 'media', 'tte_settings' );
    add_settings_section( 'tte_section', 'TED Talks', 'tte_settings_callback', 'media' );
    add_settings_field( 'tte_width', __('Width'), 'tte_width_field', 'media', 'tte_section' );
    add_settings_field( 'tte_height', __('Height'), 'tte_height_field', 'media', 'tte_section' );
    add_settings_field( 'tte_lang', __('Language', 'tedtalks'), 'tte_lang_field', 'media', 'tte_section' );
}

function tte_settings_callback() {
	esc_html_e('These default settings are used only when you omit them in your TED Talk shortcode. Leave any setting blank to let WordPress set the default values. Note that your theme styles can also alter width and height values for the embedded talks.', 'tedtalks');
}


function tte_width_field() {
	$options = get_option( 'tte_settings' );
	$width_value = $options['tte_width']; ?>

	<input name="tte_settings[tte_width]" type="number" step="1" min="0" id="tte_width" value="<?php echo $width_value ?>" class="small-text" /><br />
    <label for="tte_width"><?php esc_html_e("Don't set a value for this setting if you already set one for height. Leavy empty to let WP calculate the correct value based on your page design and visitor device.", 'tedtalks'); ?></label>	

    <?php
}

function tte_height_field() {
	$options = get_option( 'tte_settings' );
	$height_value = $options['tte_height']; ?>

	<input name="tte_settings[tte_height]" type="number" step="1" min="0" id="tte_height" value="<?php echo $height_value ?>" class="small-text" /><br />
    <label for="tte_height"><?php esc_html_e("Don't set a value for this setting if you already set one for width. Leavy empty to let WP automatically calculate the correct value based on your page design and visitor device.", 'tedtalks'); ?></label>

    <?php
}

function tte_lang_field() {
	$options = get_option( 'tte_settings' );
	$lang_value = $options['tte_lang']; ?>

    <input type="text" name="tte_settings[tte_lang]" value="<?php echo $lang_value ?>" class="small-text" id="tte_height" /><br />
    <label for="tte_lang"><?php esc_html_e("Empty for English. es for Spanish, fr for French, de for Deutsch. Check an embed code at TED.com for more language codes.", 'tedtalks'); ?></label>

    <?php
}

// TED Player embed code from Jetpack 2.8. Credit goes to Jetpack authors.
require_once ('inc/ted.php');
