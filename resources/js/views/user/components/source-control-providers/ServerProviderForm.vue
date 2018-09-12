<template>
    <label>
        <div class="providers--item" @click="selectProvider">
            <div class="providers--item-header">
                <div class="providers--item-icon"><span :class="'icon-' + provider.name.toLowerCase()"></span></div>
                <div class="providers--item-name">{{ provider.name}}</div>
            </div>
            <div class="providers--item-footer">
                <template v-if="isConnected">
                    <div class="providers--item-footer-disconnect"><h4><span class="icon-check_circle"></span> Disconnect</h4></div>
                </template>
                <template v-else>
                    <div class="providers--item-footer-connect"><h4><span class="icon-link"></span> Connect Account</h4></div>
                </template>
            </div>
        </div>
    </label>
</template>

<script>
export default {
  props: ["provider"],
  methods: {
    selectProvider() {
      if (this.isConnected) {
        return this.disconnectProvider();
      }
      this.connectProvider();
    },
    connectProvider() {
      this.$store.dispatch(
        "user/sourceControlProviders/redirectToProvider",
        this.provider.provider_name,
      );
    },
    disconnectProvider() {
      this.$store.dispatch("user/sourceControlProviders/destroy", {
        source_control_provider: this.provider.id,
      });
    },
  },
  computed: {
    isConnected() {
      return this.userRepositoryProviders.find((provider) => {
        return this.provider.id === provider.id;
      });
    },
    repositoryProviders() {
      return this.$store.state.system.sourceControlProviders.providers;
    },
    userRepositoryProviders() {
      return this.$store.state.user.sourceControlProviders.providers;
    },
  },
};
</script>
