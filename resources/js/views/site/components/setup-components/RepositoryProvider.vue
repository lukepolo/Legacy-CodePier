<template>
  <label @click="selectProvider">
    <div
      class="providers--item"
      :class="{
        'providers--item--active': isConnected.id === selected,
      }"
      v-if="isConnected"
    >
      <div class="providers--item-header">
        <div class="providers--item-icon">
          <span :class="'icon-' + repositoryProvider.name.toLowerCase()"></span>
        </div>
        <div class="providers--item-name">{{ repositoryProvider.name }}</div>
      </div>
      <div class="providers--item-footer">
        <div class="providers--item-footer-connected">
          <h4><span class="icon-check_circle"></span>Select</h4>
        </div>
      </div>
    </div>
    <template v-else>
      <source-control-provider-form
        :provider="repositoryProvider"
      ></source-control-provider-form>
    </template>
  </label>
</template>

<script>
import SourceControlProviderForm from "@views/user/components/source-control-providers/SourceControlProviderForm";

export default {
  props: {
    repositoryProvider: {
      required: true,
    },
    selected: {
      required: true,
    },
  },
  components: {
    SourceControlProviderForm,
  },
  methods: {
    selectProvider() {
      this.$emit("selectProvider", this.isConnected.id);
    },
  },
  computed: {
    userRepositoryProviders() {
      return this.$store.state.user.sourceControlProviders.providers;
    },
    isConnected() {
      return this.userRepositoryProviders.find((userRepositoryProvider) => {
        return (
          this.repositoryProvider.id ===
          userRepositoryProvider.repository_provider_id
        );
      });
    },
  },
};
</script>
