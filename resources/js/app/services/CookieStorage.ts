import { injectable } from "inversify";

@injectable()
export default class CookieStorage {
  get(item) {
    var nameEQ = item + "=";
    var ca = document.cookie.split(";");
    for (var i = 0; i < ca.length; i++) {
      var c = ca[i];
      while (c.charAt(0) == " ") c = c.substring(1, c.length);
      if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
    }
    return null;
  }

  set(item, value) {
    document.cookie = item + "=" + (value || "") + "; path=/";
  }

  remove(item) {
    document.cookie = item + "=; Max-Age=-99999999;";
  }
}
