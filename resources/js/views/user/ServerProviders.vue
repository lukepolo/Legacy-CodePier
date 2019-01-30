<template>
  <div>
    <div class="providers grid-4">
      <server-provider-form
        :provider="provider"
        v-for="provider in serverProviders"
        :key="provider.id"
      ></server-provider-form>
    </div>

    <div>
      <table class="table" v-if="userServerProviders.length">
        <thead>
          <tr>
            <th>Account</th>
            <th>Provider</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="provider in userServerProviders">
            <td>{{ provider.account }}</td>
            <td>// we can just return this info from the server</td>
            <td class="table--action">
              <tooltip message="Delete">
                <span class="table--action-delete">
                  <a @click="deleteProvider(provider)"
                    ><span class="icon-trash"></span
                  ></a>
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
import ServerProviderForm from "../common/ServerProviderForm";
export default {
  components: {
    ServerProviderForm,
  },
  created() {
    this.$store.dispatch("server/provider/get");
    this.$store.dispatch("user/servers/provider/get");
  },
  methods: {
    deleteProvider(provider) {
      this.$store.dispatch("user/serverProviders/destroy", {
        server_provider: provider.id,
      });
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
