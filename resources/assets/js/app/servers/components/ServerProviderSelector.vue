<template>

    <div class="providers grid-4">

        <label v-for="provider in server_providers">

            <template v-if="isConnected(provider.id)">
                <input
                    type="radio"
                    :value="provider.id"
                    name="server_provider_id"
                    v-model="currentServerProvider"
                >
            </template>

            <div class="providers--item" @click="connectOrSelectProvider(provider)">

                <div class="providers--item-header">
                    <div class="providers--item-icon"><span :class="'icon-' + provider.name.toLowerCase().replace(/\s+/g, '-')"></span></div>
                    <div class="providers--item-name">{{provider.name}}</div>
                </div>

                <div class="providers--item-footer">

                    <div class="providers--item-footer-connect">
                        <h4>
                            <template v-if="isConnected(provider.id)">
                                select
                            </template>
                            <template v-else>
                                <span v-if="provider.oauth">
                                   connect account
                                </span>

                                <template v-else>
                                    <server-provider-form :adding.sync="adding_provider[provider.id]" :provider="provider"></server-provider-form>
                                </template>
                            </template>
                        </h4>
                    </div>

                </div>

            </div>

        </label>

        <label>
            <template v-if="is_custom">
                <input type="hidden" name="custom" value="true">
            </template>

            <div class="providers--item" @click="selectCustom">

                <div class="providers--item-header">
                    <div class="providers--item-name">Custom</div>
                </div>

                <small>
                    This must be a fresh Ubuntu 16.04 system
                </small>

                <div class="providers--item-footer">
                    <div class="providers--item-footer-connect">
                        <h4>
                            select
                        </h4>
                    </div>
                </div>

            </div>
        </label>

    </div>

</template>

<script>
import ServerProviderForm from "./../../user/components/ServerProviderForm";
export default {
  props: ["server_provider_id", "is_custom"],
  components: {
    ServerProviderForm
  },
  data() {
    return {
      adding_provider: {},
      currentServerProvider : this.server_provider_id
    };
  },
  computed: {
    server_providers() {
      return this.$store.state.server_providers.providers;
    },
    user_server_providers() {
      return this.$store.state.user_server_providers.providers;
    }
  },
  methods: {
    selectCustom() {
      this.$emit("update:is_custom", true);
      this.$emit("update:server_provider_id", null);
    },
    connectOrSelectProvider(provider) {
      if (this.isConnected(provider.id)) {
        this.$emit("update:is_custom", false);
        this.$emit("update:server_provider_id", provider.id);
      } else {
        if (provider.oauth) {
          window.location.replace(
            this.action("AuthOauthController@newProvider", {
              provider: provider.provider_name
            })
          );
        } else {
          Vue.set(this.adding_provider, provider.id, true);
        }
      }
    },
    isConnected: function(serverProviderId) {
      return _.find(this.user_server_providers, {
        server_provider_id: serverProviderId
      });
    },
    user_repository_providers() {
      return this.$store.state.user_server_providers.providers;
    }
  },
  created() {
    this.$store.dispatch("server_providers/get");
    this.$store.dispatch(
      "user_server_providers/get",
      this.$store.state.user.user.id
    );
  }
};
</script>