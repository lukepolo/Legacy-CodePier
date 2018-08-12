import state from "./state";
import Actions from "./actions";
import Getters from "./getters";
import Mutations from "./mutations";
import { injectable } from "inversify";

@injectable()
export default class Sites {
  public name;
  public state;
  public actions;
  public getters;
  public mutations;
  public namespaced;

  constructor() {
    this.name = "Sites";
    this.state = state;
    this.namespaced = true;
    this.actions = new Actions();
    this.getters = new Getters();
    this.mutations = new Mutations();
  }
}
