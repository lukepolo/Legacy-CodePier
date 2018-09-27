<template>
    <div>
        <div class="flex" v-if="!workFlowCompleted">
            <div class="flex--grow">
                <h2>Choose Repository</h2>
                <h4 class="secondary">&nbsp;</h4>
            </div>

            <div class="heading--btns">
                <!--<delete-site :site="site"></delete-site>-->
            </div>
        </div>

        <hr v-if="!workFlowCompleted">

        <div class="providers grid-4">
            <label v-for="repositoryProvider in repositoryProviders" @click="selectProvider(isConnected(repositoryProvider).id)">
                <div class="providers--item" v-if="isConnected(repositoryProvider)">
                    <div class="providers--item-header">
                        <div class="providers--item-icon">
                            <span :class="'icon-' + repositoryProvider.name.toLowerCase()"></span>
                        </div>
                        <div class="providers--item-name">{{ repositoryProvider.name}}</div>
                    </div>
                    <div class="providers--item-footer">
                        <div class="providers--item-footer-connected"><h4><span class="icon-check_circle"></span>Select</h4></div>
                    </div>
                </div>
                <template v-else>
                    <source-control-provider-form :provider="repositoryProvider"></source-control-provider-form>
                </template>
            </label>
            <label @click="selectProvider()">
                <div class="providers--item providers--item-custom">
                    <div class="providers--item-header">
                        <div class="providers--item-name"><h3>Custom</h3></div>
                    </div>
                </div>
            </label>
        </div>
    </div>
</template>

<script>
import DeleteSite from "./../dashboard-components/DeleteSite";
import SourceControlProviderForm from "@views/user/components/source-control-providers/SourceControlProviderForm";

export default {
  props: {
    value: {
      required: true,
    },
    custom: {
      required: true,
    },
  },
  components: {
    DeleteSite,
    SourceControlProviderForm,
  },
  data() {
    return {
      user_provider: this.provider,
    };
  },
  watch: {
    provider: function(provider) {
      this.user_provider = provider;
    },
    user_provider: function(provider) {
      this.$emit("update:provider", provider);
    },
  },
  methods: {
    isConnected(provider) {
      return this.userRepositoryProviders.find((userRepositoryProvider) => {
        return provider.id === userRepositoryProvider.repository_provider_id;
      });
    },
    selectProvider(providerId) {
      this.$emit("input", providerId);
      if (!providerId) {
        this.$emit("update:custom", true);
      } else {
        this.$emit("update:custom", false);
      }
    },
  },
  computed: {
    repositoryProviders() {
      return this.$store.state.sourceControlProviders.providers;
    },
    userRepositoryProviders() {
      return this.$store.state.user.sourceControlProviders.providers;
    },
    workFlowCompleted() {
      // TODO
      return false;
    },
  },
};
</script>
