import { injectable } from "inversify";

@injectable()
export default class LocalStorage {
  private $storage;

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
