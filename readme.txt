=== Freshet Edit Jump ===
Contributors: kristoffbertram
Tags: admin bar, edit, keyboard shortcut, productivity
Requires at least: 6.0
Tested up to: 7.1
Requires PHP: 7.4
Stable tag: 1.1.0
License: MIT
License URI: https://opensource.org/licenses/MIT

Hides WP's admin bar for users who can edit content, and adds a keyboard shortcut (Cmd+E / Ctrl+E, or Alt+Shift+E) to jump directly to edit.

== Description ==

The admin bar takes 32 pixels off every logged-in page view to offer buttons you rarely click. Freshet Edit Jump removes it and replaces the one thing you actually use — "Edit this page" — with a keyboard shortcut:

* **Cmd+E** (Mac) / **Ctrl+E** (Windows/Linux) — jump straight to the edit screen of the page you are viewing.
* **Alt+Shift+E** — universal fallback, for when the primary combination is already taken.
* **Cmd/Ctrl+Alt+E** — toggle the admin bar back on. Per browser, via a cookie, so it survives a reload and affects nobody else.

The jump only fires on views with a real edit target — singular views, plus the blog index when a static posts page is set — and only where you actually have permission to edit that post. The admin bar is only hidden for users who can edit content; other logged-in roles (members, subscribers) keep it untouched. Shortcuts stay out of the way while you are typing in a field or a rich-text editor.

There is no settings screen, no dashboard widget and no front-end markup beyond one small script for logged-in users.

Part of the Freshet plugin suite. Full documentation: [freshet.studio/docs](https://freshet.studio/docs).

== Installation ==

1. Install and activate the plugin.
2. Load any page of your site while logged in as a user who can edit content. The admin bar is gone; press Cmd/Ctrl+E to edit what you are looking at.

There is nothing to configure.

== Frequently Asked Questions ==

= I need the admin bar back =

Press Cmd/Ctrl+Alt+E to toggle it back on for your browser, or deactivate the plugin. The toggle is a cookie, so it lasts 30 days and changes nothing for other users.

= Does it hide the admin bar for everyone? =

No. Only for users who can edit content (`edit_posts`). Subscribers, customers and members keep the bar and its account links.

= Nothing happens when I press the shortcut =

Then the page has no edit target for you: an archive, a search result, a 404, or a post you cannot edit. The plugin does not guess — it only jumps where WordPress gives it a real edit URL. If the combination itself is taken by your browser or OS, use the Alt+Shift+E fallback.

= Does it change anything for logged-out visitors? =

No. It loads nothing at all for them.

== Changelog ==

= 1.1.0 =
* Admin bar is now only hidden for users who can edit content (edit_posts); other logged-in roles keep it.
* Jump also works on the blog index when a static posts page is set.
* Version string centralised in FRESHET_EDITJUMP_VERSION.

= 1.0.1 =
* Move inline script to an asset file; plugin-check clean.

= 1.0.0 =
* Initial release.
