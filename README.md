# Freshet Edit Jump

Hides WordPress' front-end admin bar for users who can edit content, and jumps straight to the edit screen with a keyboard shortcut.

Part of the Freshet plugin suite. MIT.

The admin bar takes 32 pixels off every logged-in page view, injects CSS, shifts
layout and breaks pixel-perfect front-end work — to offer buttons you rarely
click. This removes it and replaces the one thing you actually use, "Edit this
page", with a key combination.

## Features

- Hides the front-end admin bar — but only for users who can `edit_posts`.
  Subscribers, customers and members keep it and its account links.
- No `_admin_bar_bump_cb` removal, so the top margin stays correct for the
  roles that still see the bar. No layout shift either way.
- Jump straight to the edit screen of the post you are looking at.
- OS-aware shortcuts:
  - **macOS:** `Cmd + E`
  - **Windows / Linux:** `Ctrl + E`
  - **Fallback (all):** `Alt + Shift + E`
- Toggle the bar back on without deactivating anything (a 30-day cookie, per
  browser):
  - **macOS:** `Cmd + Option + E`
  - **Windows / Linux:** `Ctrl + Alt + E`

## Usage

### Jump to edit (front end only)

| OS | Shortcut |
|----|----------|
| macOS | `Cmd + E` |
| Windows / Linux | `Ctrl + E` |
| All | `Alt + Shift + E` |

Fires only where WordPress gives a real edit URL: singular views, plus the blog
index when a static posts page is set — and only where the current user can
edit that post. On an archive, a search result or a 404 the shortcut does
nothing.

### Toggle the admin bar back on

| OS | Shortcut |
|----|----------|
| macOS | `Cmd + Option + E` |
| Windows / Linux | `Ctrl + Alt + E` |

Sets a cookie and reloads. It affects your browser only, and the toggle stays
available even while the plugin is soft-disabled.

## Technical notes

- Front end only; nothing is enqueued for logged-out visitors.
- Shortcut matching is on `event.code` (physical key), not `event.key` —
  Option/Alt rewrites `key` on macOS.
- Keystrokes are ignored while focus is in an input, textarea, select or any
  `contenteditable` element.
- The edit URL is resolved server-side against `edit_post` and handed to the
  script as data; the script never constructs an admin URL of its own.

## Dev environment

Symlink or copy the plugin into a local WordPress install and activate it:

```bash
ln -s "$(pwd)" /path/to/wp/wp-content/plugins/freshet-editjump
```

No build step — plain PHP (7.4+) and one plain JS file in `assets/`.

Lint: `php -l freshet-editjump.php`.

## License

MIT. Part of the [Freshet Studio](https://freshet.studio) plugin suite.
