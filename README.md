# The Edit Jump

_1.0.0_

Hide WordPress’ front-end admin bar and jump **directly to the edit screen** with a keyboard shortcut.

## Features

- Hides the front-end admin bar (no top margin injection → no layout shift)
- Jump directly to the edit screen of the current post/page
- OS-aware keyboard shortcuts:
  - **macOS:** `⌘ + E`
  - **Windows / Linux:** `Ctrl + E`
  - **Fallback (all):** `Alt + Shift + E`
- Instant toggle to bring the admin bar back (soft-disable):
  - **macOS:** `⌘ + ⌥ + E`
  - **Windows / Linux:** `Ctrl + Alt + E`

## Usage

### Jump to edit (front-end only)
| OS | Shortcut |
|----|----------|
| macOS | `⌘ + E` |
| Windows / Linux | `Ctrl + E` |
| All | `Alt + Shift + E` |

Only works on **singular views** (posts/pages you can edit).

### Toggle admin bar back on/off
| OS | Shortcut |
|----|----------|
| macOS | `⌘ + ⌥ + E` |
| Windows / Linux | `Ctrl + Alt + E` |

Uses a cookie — no plugin deactivation required. 

## Why this exists

The WordPress admin bar is useful — but it:
- injects CSS
- shifts layouts
- breaks pixel-perfect front-end work

This plugin removes the bar entirely and replaces it with something your keyboard.

## Technical notes

- Front-end only
- Respects user permissions
- Does nothing when disabled

## License

MIT — see `LICENSE`.

## Changelog

- 1.0.0 Release.

## Disclaimer
- Built out of personal necessity. If you actively work with WordPress, you’ll like this.
- There is no dashboard fallback, visual indicator, editor integration or configuration UI, which is intentional.
- I take absolutely no responsibility for confusion.