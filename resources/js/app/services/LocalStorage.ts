import { injectable } from "inversify";

@injectable()
export default class LocalStorage {
  private $storage;

  // TODO - Need to figure out how to protect this token from users in the browser
  // Removal of $app would do it.... but removes our helper (is that a bad thing)
  constructor() {
    this.$storage = window.localStorage;
    // @ts-ignore
    // delete window.localStorage;
  }

  get(item) {
    return this.$storage.getItem(item);
  }

  set(item, value) {
    this.$storage.setItem(item, value);
  }

  remove(item) {
    this.$storage.removeItem(item);
  }
}
