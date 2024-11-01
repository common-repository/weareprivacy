<?php
/**
* Plugin Name: WeArePrivacy
* Plugin URI: https://weareprivacy.com/
* Description: Auto highlight important keywords on any privacy policy or terms of service to quickly find and understand critical sections.
* Version: 1.0.3
* Author: WeArePrivacy - Pawel Glowacki
* Author URI: https://twitter.com/PawelGlow
**/

add_action('admin_menu', 'weareprivacy_menu');

function weareprivacy_defaults() {
	return array(
		'page_triggers_default' => 'privacy,terms,disclosure,disclaimer,cookie,conduct,policy,policies,legal,sign,contract,confirm,agreement,gdpr'
	);
}

function weareprivacy_menu() {

	//create new top-level menu
	add_menu_page('WeArePrivacy Plugin Settings', 'WeArePrivacy', 'administrator', __FILE__, 'weareprivacy_settings_page',plugins_url('/sidebar-icon.svg', __FILE__));

	//call register settings function
	add_action( 'admin_init', 'register_weareprivacy_settings' );
}

function register_weareprivacy_settings() {
	//register our settings
	register_setting( 'weareprivacy-settings-group', 'weareprivacy_policy_highlights_enabled' );
	register_setting( 'weareprivacy-settings-group', 'weareprivacy_policy_highlights_page_triggers' );
}

function weareprivacy_settings_page() {
	$defaults = weareprivacy_defaults();
?>

<div class="wrap">
	<h2>WeArePrivacy</h2>
	<form method="post" action="options.php">
	<?php settings_fields( 'weareprivacy-settings-group' ); ?>

	<h3>Policy Highlights</h3>

	<table class="form-table">
		<tr valign="top">
			<th scope="row">Enable / disable</th>
			<td><input type="checkbox" name="weareprivacy_policy_highlights_enabled" value="1" <?php checked(1, get_option('weareprivacy_policy_highlights_enabled', 1), true); ?> /></td>
		</tr>
		<tr valign="top">
			<th scope="row">Page triggers</th>
			<td><input type="text" name="weareprivacy_policy_highlights_page_triggers" value="<?php echo get_option('weareprivacy_policy_highlights_page_triggers', $defaults['page_triggers_default']); ?>" /></td>
		</tr>
	</table>

	<p class="submit">
		<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>

	</form>
</div>
<?php }

function weareprivacy_policy_highlights_plugin($hook) {
	$enabled = get_option('weareprivacy_policy_highlights_enabled', true);
	if ($enabled !== FALSE && $enabled) {
		$defaults = weareprivacy_defaults();
		$triggers = get_option('weareprivacy_policy_highlights_page_triggers', $defaults['page_triggers_default']);
		$title = esc_html( get_the_title() );
		$url = esc_url( get_permalink() );
		$keywords = implode(' ', array($title, $url));
		$matches = array();
		$pattern = '/' . implode('|', explode(',', $triggers)) . '/i';
		preg_match($pattern, $keywords, $matches);
		if ($matches) {
		    $srcCSS = plugins_url('/index.css', __FILE__);
            wp_enqueue_script( 'weareprivacy-policy-highlights-css', $srcCSS, array(), null, true );
			$srcJS = plugins_url('/index.js', __FILE__);
		    wp_enqueue_script( 'weareprivacy-policy-highlights-js', $srcJS, array(), null, true );
		}
	}
}

add_action('wp_enqueue_scripts', 'weareprivacy_policy_highlights_plugin');