<?php
/**
 * Plugin Name:       Freshet Edit Jump
 * Plugin URI:        https://freshet.studio
 * Description:       Hides WP's admin bar and adds a keyboard shortcut (⌘+E / Ctrl+E, or Alt+Shift+E) to jump directly to edit.
 * Version:           1.0.1
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Freshet Studio
 * Author URI:        https://freshet.studio
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       freshet-editjump
 */

defined('ABSPATH') || exit;

function freshet_editjump_is_disabled(): bool {
	// Soft-disable via cookie so you can toggle without touching WP plugins.
	return isset($_COOKIE['freshet_editjump_disabled']) && $_COOKIE['freshet_editjump_disabled'] === '1';
}

add_action('after_setup_theme', function () {
	// Front-end only.
	if ( is_admin() ) {
		return;
	}

	// If disabled, do nothing (admin bar remains).
	if ( freshet_editjump_is_disabled() ) {
		return;
	}

	// Hide admin bar on the front-end.
	add_filter('show_admin_bar', '__return_false', 1000);
	// Defensive: prevents the <html> margin-top "bump" CSS.
	remove_action('wp_head', '_admin_bar_bump_cb');
}, 20);

add_action('wp_enqueue_scripts', function () {
	if ( is_admin() || ! is_user_logged_in() ) {
		return;
	}

	$disabled = freshet_editjump_is_disabled();

	// Only allow "jump to edit" on singular screens with a real edit link.
	$edit_url = null;
	if ( ! $disabled && is_singular() ) {
		$post_id = get_queried_object_id();
		if ( $post_id && current_user_can('edit_post', $post_id) ) {
			$edit_url = get_edit_post_link($post_id, 'raw'); // precise edit screen
		}
	}

	wp_register_script('freshet-editjump', plugins_url('assets/freshet-editjump.js', __FILE__), [], '1.0.1', true);
	wp_enqueue_script('freshet-editjump');

	wp_add_inline_script(
		'freshet-editjump',
		'window.freshetEditJump = ' . wp_json_encode([
			'editUrl'   => $edit_url,         // null when not applicable
			'disabled'  => $disabled,
			'cookieKey' => 'freshet_editjump_disabled',
		]) . ';',
		'before'
	);

}, 20);
