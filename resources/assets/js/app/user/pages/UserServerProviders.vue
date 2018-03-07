<template>

    <div>
        <div class="providers grid-4">

            <label v-for="provider in server_providers">

                <div class="providers--item" @click="connectProvider(provider)">

                    <div class="providers--item-header">
                        <div class="providers--item-icon"><span :class="'icon-' + provider.name.toLowerCase().replace(/\s+/g, '-')"></span></div>
                        <div class="providers--item-name">{{provider.name}}</div>
                    </div>

                    <div class="providers--item-footer">
                        <div class="providers--item-footer-connect">
                            <h4>
                            <span v-if="provider.oauth">
                               <span class="icon-link"></span> Connect A New Account
                            </span>

                                <template v-else>
                                    <server-provider-form :adding.sync="adding_provider[provider.id]" :provider="provider"></server-provider-form>
                                </template>
                            </h4>
                        </div>
                    </div>

                </div>

                <div class="text-center" v-if="!isConnected(provider.id) && provider.name === 'Digital Ocean'">
                    <a target="_blank" href="https://m.do.co/c/27ffab8712be">Get Free $10 for <strong>New Accounts</strong></a>
                </div>

            </label>

        </div>

        <div>
            <table class="table" v-if="user_server_providers.length">
                <thead>
                    <tr>
                        <th>Account</th>
                        <th>Provider</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="provider in user_server_providers">
                        <td>{{ provider.account }}</td>
                        <td>{{ provider.provider }}</td>
                        <td class="table--action">
                            <tooltip message="Delete">
                            <span class="table--action-delete">
                                <a @click="deleteProvider(provider)"><span class="icon-trash"></span></a>
                            </span>
                            </tooltip>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</template>

<script>
import ServerProviderForm from "../components/ServerProviderForm";
export default {
  props: [],
  components: {
    ServerProviderForm
  },
  data() {
    return {
      adding_provider: {}
    };
  },
  created() {
    this.$store.dispatch(
      "user_server_providers/get",
      this.$store.state.user.user.id
    );
  },
  computed: {
    server_providers() {
      return this.$store.state.server_providers.providers;
    },
    user_server_providers() {
      return this.$store.state.user_server_providers.providers;
    },
  },
  methods: {
    connectProvider(provider) {
        Vue.set(this.adding_provider, provider.id, true);
    },
    isConnected: function(server_provider_id) {
      return _.find(this.user_server_providers, {
        server_provider_id: server_provider_id
      });
    },
    deleteProvider(provider) {
      this.$store.dispatch("user_server_providers/destroy", {
        server_provider: provider.id,
        user: this.$store.state.user.user.id,
      });
    },
  }
};
</script>
