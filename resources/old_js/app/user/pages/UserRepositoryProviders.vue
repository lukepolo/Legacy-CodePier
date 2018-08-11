<template>
    <div class="providers grid-6">
        <label v-for="repository_provider in repository_providers">
            <div class="providers--item" @click="!isConnected(repository_provider.id) ? registerProvider(repository_provider.provider_name) : disconnectProvider(repository_provider.id)">
                <div class="providers--item-header">
                    <div class="providers--item-icon"><span :class="'icon-' + repository_provider.name.toLowerCase()"></span></div>
                    <div class="providers--item-name"> {{ repository_provider.name}}</div>
                </div>
                <div class="providers--item-footer">
                    <template v-if="isConnected(repository_provider.id)">
                        <div class="providers--item-footer-disconnect"><h4><span class="icon-check_circle"></span> Disconnect</h4></div>
                    </template>
                    <template v-else>
                        <div class="providers--item-footer-connect"><h4><span class="icon-link"></span> Connect Account</h4></div>
                    </template>
                </div>
            </div>
        </label>
    </div>
</template>

<script>
export default {
  created() {
    this.$store.dispatch(
      "user_repository_providers/get",
      this.$store.state.user.user.id,
    );
  },
  computed: {
    repository_providers() {
      return this.$store.state.repository_providers.providers;
    },
    user_repository_providers() {
      return this.$store.state.user_repository_providers.providers;
    },
  },
  methods: {
    isConnected: function(repository_provider_id) {
      return _.find(this.user_repository_providers, {
        repository_provider_id: parseInt(repository_provider_id),
      });
    },
    disconnectProvider: function(repository_provider_id) {
      let repository_provider = _.find(this.user_repository_providers, function(
        repository_provider,
      ) {
        return (
          repository_provider.repository_provider_id === repository_provider_id
        );
      }).id;

      this.$store.dispatch("user_repository_providers/destroy", {
        user: this.$store.state.user.user.id,
        repository_provider: repository_provider,
      });
    },
    registerProvider(provider) {
      window.location.replace(
        this.action("AuthOauthController@newProvider", {
          provider: provider,
        }),
      );
    },
  },
};
</script>
