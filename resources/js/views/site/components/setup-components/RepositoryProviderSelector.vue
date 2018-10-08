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
            <repository-provider
                :selected="value"
                v-on:selectProvider="selectCustomProvider"
                :repositoryProvider="repositoryProvider"
                v-for="repositoryProvider in repositoryProviders"
                :key="repositoryProvider.id"
            ></repository-provider>
            <label @click="selectCustomProvider()">
                <div
                    class="providers--item providers--item-custom"
                    :class="{
                        'providers--item--active' : custom
                    }"
                >
                    <div class="providers--item-header">
                        <div class="providers--item-name"><h3>Custom</h3></div>
                    </div>
                </div>
            </label>
        </div>
    </div>
</template>

<script>
import RepositoryProvider from "./RepositoryProvider";
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
    RepositoryProvider,
    SourceControlProviderForm,
  },
  methods: {
    selectCustomProvider(providerId) {
      this.$emit("input", providerId);
      if (!providerId) {
        this.$emit("update:custom", true);
      } else {
        this.$emit("update:custom", false);
      }
    },
  },
  computed: {
    workFlowCompleted() {
      // TODO
      return false;
    },
    repositoryProviders() {
      return this.$store.state.sourceControlProviders.providers;
    },
  },
};
</script>
