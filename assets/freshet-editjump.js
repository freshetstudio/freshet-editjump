(function () {
  var cfg = window.freshetEditJump || {};
  var cookieKey = cfg.cookieKey || 'freshet_editjump_disabled';

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
