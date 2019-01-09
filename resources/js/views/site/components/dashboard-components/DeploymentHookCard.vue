<template>
  <drop-down tag="span">
    <div class="grid--item" slot="header">
      <div class="providers--item">
        <div class="providers--item-header">
          <div class="providers--item-icon">
            <span class="icon-webhooks"></span>
          </div>
        </div>
        <div class="providers--item-footer">
          <div class="providers--item-footer-connect">
            <h4>Deploy Hook URL</h4>
          </div>
        </div>
      </div>
    </div>
    <div slot="content" class="dropdown-menu dropdown-content nowrap">
      <h3>
        // TODO
        <!--<confirm-dropdown dispatch="user_site_deployments/refreshDeployKey" :params="site.id">-->
        <!--Deploy Hook URL &nbsp;-->
        <!--<tooltip message="Refresh Deploy Key">-->
        <!--<a @click.prevent href="#"><span class="fa fa-refresh"></span></a>-->
        <!--</tooltip>-->
        <!--</confirm-dropdown>-->
      </h3>

      <div class="flyform--group flyform--group-nomargin">
        <textarea rows="3" readonly :value="deployHook"></textarea>
      </div>

      <div class="text-right">
        <tooltip message="Copy to Clipboard">
          <clipboard :data="deployHook"></clipboard>
        </tooltip>
      </div>
    </div>
  </drop-down>
</template>

<script>
export default {
  props: ["site"],
  methods: {
    createDeployHook() {
      return this.$store.dispatch(
        "user_site_deployments/createDeployHook",
        this.$route.params.site_id,
      );
    },
    removeDeployHook() {
      this.$store.dispatch("user_site_deployments/removeDeployHook", {
        site: this.$route.params.site_id,
        hook: this.site.automatic_deployment_id,
      });
    },
  },
  computed: {
    deployHook() {
      return "todo";
      if (this.site) {
        return (
          location.protocol +
          "//" +
          location.hostname +
          Vue.action("WebHookController@deploy", { siteHashId: this.site.hash })
        );
      }
    },
  },
};
</script>
