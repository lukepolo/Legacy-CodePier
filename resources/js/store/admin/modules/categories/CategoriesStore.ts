import state from "./state";
import { injectable, inject } from "inversify";
import RestStoreModule from "@app/extensions/RestStoreModule/RestStoreModule";

@injectable()
export default class CategoriesStore extends RestStoreModule {
  constructor(@inject("CategoryService") categoryService) {
    super(categoryService, "categories");
    this.setName("categories").addState(state);
  }
}
