import { injectable } from "inversify";

// https://developer.mozilla.org/en-US/docs/Web/API/Document/cookie/Simple_document.cookie_framework

@injectable()
export default class CookieStorage {
  get(name) {
    if (!name) {
      return null;
    }
    return (
      decodeURIComponent(
        document.cookie.replace(
          new RegExp(
            "(?:(?:^|.*;)\\s*" +
              encodeURIComponent(name).replace(/[\-\.\+\*]/g, "\\$&") +
              "\\s*\\=\\s*([^;]*).*$)|^.*$",
          ),
          "$1",
        ),
      ) || null
    );
  }

  set(name, value, days = 1, path = "/", domain?, secure?) {
    let expires = new Date();
    expires.setTime(expires.getTime() + days * 24 * 60 * 60 * 1000);
    if (!name || /^(?:expires|max\-age|path|domain|secure)$/i.test(name)) {
      return false;
    }
    document.cookie = `${encodeURIComponent(name)}=${encodeURIComponent(
      value,
    )}; expires=${expires.toUTCString()}${domain ? "; domain=" + domain : ""}${
      path ? "; path=" + path : ""
    }${secure ? "; secure" : ""}`;
    return true;
  }

  remove(name, path?, domain?) {
    if (!this.hasItem(name)) {
      return false;
    }
    document.cookie =
      encodeURIComponent(name) +
      "=; expires=Thu, 01 Jan 1970 00:00:00 GMT" +
      (domain ? "; domain=" + domain : "") +
      (path ? "; path=" + path : "");
    return true;
  }

  hasItem(name) {
    if (!name || /^(?:expires|max\-age|path|domain|secure)$/i.test(name)) {
      return false;
    }
    return new RegExp(
      "(?:^|;\\s*)" +
        encodeURIComponent(name).replace(/[\-\.\+\*]/g, "\\$&") +
        "\\s*\\=",
    ).test(document.cookie);
  }

  keys() {
    let aKeys = document.cookie
      .replace(/((?:^|\s*;)[^\=]+)(?=;|$)|^\s*|\s*(?:\=[^;]*)?(?:\1|$)/g, "")
      .split(/\s*(?:\=[^;]*)?;\s*/);
    for (let nLen = aKeys.length, nIdx = 0; nIdx < nLen; nIdx++) {
      aKeys[nIdx] = decodeURIComponent(aKeys[nIdx]);
    }
    return aKeys;
  }
}
