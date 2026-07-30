<?php
/**
 * Plugin Name:       Freshet Edit Jump
 * Plugin URI:        https://freshet.studio
 * Description:       Hides WP's admin bar for users who can edit content, and adds a keyboard shortcut (⌘+E / Ctrl+E, or Alt+Shift+E) to jump directly to edit.
 * Version:           1.1.0
 * Requires at least: 6.0
 * Requires PHP:      7.4
 * Author:            Freshet Studio
 * Author URI:        https://freshet.studio
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       freshet-editjump
 */

defined('ABSPATH') || exit;

// Single version source for enqueues; keep in sync with the header + readme
// stable tag (portfolio convention, same as freshet-feeds / freshet-unusedmedia).
define('FRESHET_EDITJUMP_VERSION', '1.1.0');

function freshet_editjump_is_disabled(): bool {
	// Soft-disable via cookie so you can toggle without touching WP plugins.
	return isset($_COOKIE['freshet_editjump_disabled']) && wp_unslash($_COOKIE['freshet_editjump_disabled']) === '1';
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

	// Hide the admin bar, but only for users who can actually edit content —
	// non-editor roles (members, subscribers) keep the bar and its account
	// links. Capability is checked at filter time, not here: the current user
	// isn't set up yet on after_setup_theme.
	add_filter('show_admin_bar', function (bool $show): bool {
		return current_user_can('edit_posts') ? false : $show;
	}, 1000);
	// No manual _admin_bar_bump_cb removal: core only adds the bump CSS when
	// is_admin_bar_showing() is true, and removing it unconditionally would
	// break the top margin for non-editors who keep the bar.
}, 20);

add_action('wp_enqueue_scripts', function () {
	if ( is_admin() || ! is_user_logged_in() ) {
		return;
	}

	$disabled = freshet_editjump_is_disabled();

	// Only allow "jump to edit" on screens with a real edit target: singular
	// views, or the blog index when a static posts page is set (is_singular()
	// is false there, but the posts page itself is editable).
	$edit_url = null;
	if ( ! $disabled ) {
		$post_id = 0;
		if ( is_singular() ) {
			$post_id = get_queried_object_id();
		} elseif ( is_home() && 'page' === get_option('show_on_front') ) {
			$post_id = (int) get_option('page_for_posts');
		}
		if ( $post_id && current_user_can('edit_post', $post_id) ) {
			$edit_url = get_edit_post_link($post_id, 'raw'); // precise edit screen
		}
	}

	wp_register_script('freshet-editjump', plugins_url('assets/freshet-editjump.js', __FILE__), [], FRESHET_EDITJUMP_VERSION, true);
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
