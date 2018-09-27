<template>
    <div class="providers grid-6">
       <source-control-provider-form :provider="repositoryProvider" v-for="repositoryProvider in repositoryProviders" :key="repositoryProvider.id"></source-control-provider-form>
    </div>
</template>

<script>
import SourceControlProviderForm from "./components/source-control-providers/SourceControlProviderForm";
export default {
  components: {
    SourceControlProviderForm,
  },
  created() {
    this.$store.dispatch("sourceControlProviders/get");
    this.$store.dispatch("user/sourceControlProviders/get");
  },
  computed: {
    repositoryProviders() {
      return this.$store.state.sourceControlProviders.providers;
    },
  },
};
</script>
