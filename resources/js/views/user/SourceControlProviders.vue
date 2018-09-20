<template>
    <div class="providers grid-6">
       <source-control-provider :provider="repositoryProvider" v-for="repositoryProvider in repositoryProviders" :key="repositoryProvider.id"></source-control-provider>
    </div>
</template>

<script>
import SourceControlProvider from "./components/source-control-providers/ServerProviderForm";
export default {
  components: {
    SourceControlProvider,
  },
  created() {
    this.$store.dispatch("user/sourceControlProviders/get");
    this.$store.dispatch("system/sourceControlProviders/get");
  },
  computed: {
    repositoryProviders() {
      return this.$store.state.system.sourceControlProviders.providers;
    },
  },
};
</script>
