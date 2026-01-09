<?php
/**
 * Plugin Name: The Edit Jump
 * Description: Hides WP's admin bar and adds a keyboard shortcut (⌘+E / Ctrl+E, or Alt+Shift+E) to jump directly to edit.
 * Author: Kristoff Bertram
 * Author URI: https://kristoffbertram.be
 * Plugin URI: https://github.com/kristoffbertram/theeditjump
 * Version: 1.0.0
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

	wp_register_script('theeditjump', '', [], '0.4.0', true);
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

	wp_add_inline_script('theeditjump', <<<JS
(function () {
  var cfg = window.theeditjump || {};
  var cookieKey = cfg.cookieKey || 'theeditjump_disabled';

  // Robust platform detection
  var platform = '';
  try {
    platform = (navigator.userAgentData && navigator.userAgentData.platform)
      ? navigator.userAgentData.platform
      : (navigator.platform || '');
  } catch (e) {}
  var isMac = /mac/i.test(platform);

  // Primary jump shortcut:
  // - Mac:    Cmd + E
  // - Win/*:  Ctrl + E
  var jumpPrimary = isMac
    ? { meta:true,  ctrl:false, alt:false, shift:false, code:'KeyE' }
    : { meta:false, ctrl:true,  alt:false, shift:false, code:'KeyE' };

  // Secondary jump shortcut (universal):
  // Alt + Shift + E
  var jumpSecondary = { meta:false, ctrl:false, alt:true, shift:true, code:'KeyE' };

  // Toggle shortcut (universal, avoids common browser bindings):
  // Cmd/Ctrl + Alt + E  => toggles admin bar hiding on/off (soft disable)
  var toggleShortcut = isMac
    ? { meta:true,  ctrl:false, alt:true, shift:false, code:'KeyE' }
    : { meta:false, ctrl:true,  alt:true, shift:false, code:'KeyE' };

  function isTyping(el) {
    if (!el) return false;
    var tag = (el.tagName || '').toLowerCase();
    return tag === 'input' || tag === 'textarea' || tag === 'select' || el.isContentEditable;
  }

  function matchShortcut(e, s) {
    if (!s) return false;
    if (!!s.meta  !== e.metaKey)  return false;
    if (!!s.ctrl  !== e.ctrlKey)  return false;
    if (!!s.alt   !== e.altKey)   return false;
    if (!!s.shift !== e.shiftKey) return false;
    // IMPORTANT: physical key identity (Option/Alt can alter e.key on Mac).
    return e.code === s.code;
  }

  function setCookie(value) {
    // 30 days, path=/ so it affects the whole site
    var maxAge = 60 * 60 * 24 * 30;
    document.cookie = cookieKey + '=' + value + '; path=/; max-age=' + maxAge + '; samesite=lax';
  }

  document.addEventListener('keydown', function (e) {
    if (isTyping(document.activeElement)) return;

    // Toggle mode always available (even when disabled)
    if (matchShortcut(e, toggleShortcut)) {
      e.preventDefault();
      setCookie(cfg.disabled ? '0' : '1');
      window.location.reload();
      return;
    }

    // If disabled, do not jump.
    if (cfg.disabled) return;

    // Jump only when we have an actual edit URL (singular + permission).
    if (!cfg.editUrl) return;

    if (!matchShortcut(e, jumpPrimary) && !matchShortcut(e, jumpSecondary)) return;

    e.preventDefault();
    window.location.href = cfg.editUrl;
  }, { passive: false });
})();
JS, 'after');
}, 20);