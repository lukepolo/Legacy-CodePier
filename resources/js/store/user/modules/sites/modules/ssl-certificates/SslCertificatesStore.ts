import state from "./state";
import actions from "./actions";
import getters from "./getters";
import mutations from "./mutations";
import { injectable, inject, unmanaged } from "inversify";
import RestStoreModule from "@app/extensions/RestStoreModule/RestStoreModule";

@injectable()
export default class UserSiteSslCertificateStore extends RestStoreModule {
  constructor(@inject("SiteSslCertificateService") siteSslCertificateService) {
    super(siteSslCertificateService, "ssl_certificates");
    this.setName("sslCertificates")
      .addState(state)
      .addActions(actions())
      .addMutations(mutations)
      .addGetters(getters);
  }
}
