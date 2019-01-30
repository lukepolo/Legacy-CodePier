<template>
  <div class="providers grid-4">
    <template v-for="provider in serverProviders">
      <label @click="selectProvider">
        <div class="providers--item">
          <div class="providers--item-header">
            <div class="providers--item-icon">
              <span
                :class="
                  'icon-' + provider.name.toLowerCase().replace(/\s+/g, '-')
                "
              ></span>
            </div>
            <div class="providers--item-name">{{ provider.name }}</div>
          </div>
          <div class="providers--item-footer">
            <div class="providers--item-footer-connect">
              <h4>
                <template v-if="isConnected(provider.id)">
                  <div class="providers--item-footer-connected">
                    <h4><span class="icon-check_circle"></span> Select</h4>
                  </div>
                </template>
                <template v-else>
                  <span v-if="provider.oauth">
                    connect account
                  </span>

                  <template v-else>
                    <server-provider-form
                      :provider="provider"
                    ></server-provider-form>
                  </template>
                </template>
              </h4>
            </div>
          </div>
        </div>
      </label>
    </template>

    <label>
      <template v-if="is_custom">
        <input type="radio" name="custom" value="true" checked />
      </template>

      <div class="providers--item" @click="selectCustom">
        <div class="providers--item-header">
          <div class="providers--item-name">Custom</div>
        </div>

        <small>
          This must be a fresh Ubuntu system
        </small>

        <div class="providers--item-footer">
          <div class="providers--item-footer-connect">
            <h4>
              <div class="providers--item-footer-connected">
                <h4>Select</h4>
              </div>
            </h4>
          </div>
        </div>
      </div>
    </label>
  </div>
</template>

<script>
import ServerProviderForm from "@views/common/ServerProviderForm";
export default {
  props: {
    value: {
      required: true,
    },
  },
  components: {
    ServerProviderForm,
  },
  data() {
    return {
      adding_provider: {},
      currentServerProvider: this.server_provider_id,
    };
  },
  created() {
    this.$store.dispatch("server/provider/get");
    this.$store.dispatch("user/servers/provider/get");
  },
  methods: {
    selectCustom() {
      // this.currentServerProvider = null;
      // this.$emit("update:is_custom", true);
      // this.$emit("update:server_provider_id", null);
    },
    connectOrSelectProvider(provider) {
      // if (this.isConnected(provider.id)) {
      //   this.$emit("update:is_custom", false);
      //   this.$emit("update:server_provider_id", provider.id);
      // } else {
      //   if (provider.oauth) {
      //     window.location.replace(
      //       this.action("AuthOauthController@newProvider", {
      //         provider: provider.provider_name
      //       })
      //     );
      //   } else {
      //     Vue.set(this.adding_provider, provider.id, true);
      //   }
      // }
    },
    isConnected: function(serverProviderId) {
      // return _.find(this.user_server_providers, {
      //   server_provider_id: serverProviderId
      // });
    },
    user_repository_providers() {
      // return this.$store.state.user_server_providers.providers;
    },
  },
  computed: {
    serverProviders() {
      return this.$store.state.server.provider.providers;
    },
    userServerProviders() {
      return this.$store.state.user.servers.provider.providers;
    },
  },
};
</script>
