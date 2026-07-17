<?php
/**
 * Plugin Name: The Edit Jump
 * Description: Hides WP's admin bar and adds a keyboard shortcut (⌘+E / Ctrl+E, or Alt+Shift+E) to jump directly to edit.
 * Author: Kristoff Bertram
 * Author URI: https://kristoffbertram.be
 * Plugin URI: https://github.com/kristoffbertram/theeditjump
 * Version: 1.0.1
 * License: MIT
 * License URI: https://opensource.org/licenses/MIT
 */

defined('ABSPATH') || exit;

function theeditjump_is_disabled(): bool {
	// Soft-disable via cookie so you can toggle without touching WP plugins.
	return isset($_COOKIE['theeditjump_disabled']) && $_COOKIE['theeditjump_disabled'] === '1';
}

add_action('after_setup_theme', function () {
	// Front-end only.
	if ( is_admin() ) {
		return;
	}

	// If disabled, do nothing (admin bar remains).
	if ( theeditjump_is_disabled() ) {
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

	$disabled = theeditjump_is_disabled();

	// Only allow "jump to edit" on singular screens with a real edit link.
	$edit_url = null;
	if ( ! $disabled && is_singular() ) {
		$post_id = get_queried_object_id();
		if ( $post_id && current_user_can('edit_post', $post_id) ) {
			$edit_url = get_edit_post_link($post_id, 'raw'); // precise edit screen
		}
	}

	wp_register_script('theeditjump', plugins_url('assets/theeditjump.js', __FILE__), [], '1.0.1', true);
	wp_enqueue_script('theeditjump');

	wp_add_inline_script(
		'theeditjump',
		'window.theeditjump = ' . wp_json_encode([
			'editUrl'   => $edit_url,         // null when not applicable
			'disabled'  => $disabled,
			'cookieKey' => 'theeditjump_disabled',
		]) . ';',
		'before'
	);

}, 20);