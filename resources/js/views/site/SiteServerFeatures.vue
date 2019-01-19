<template>
  <base-form
    :action="updateSiteServerFeatures"
    enctype="multipart/form-data"
    class="floating-labels"
  >
    <server-features v-model="selectedServerFeatures"></server-features>
    <template slot="buttons">
      <div class="flyform--footer-btns">
        <button class="btn btn-primary" type="submit">
          Update Site Server Features
        </button>
      </div>
    </template>
  </base-form>
</template>

<script>
import ServerFeatures from "../common/ServerFeatures";

export default {
  components: {
    ServerFeatures,
  },
  watch: {
    $route: {
      immediate: true,
      handler() {
        this.$store.dispatch("user/sites/servers/features/get", {
          site: this.siteId,
        });
      },
    },
  },
  methods: {
    updateSiteServerFeatures() {
      // this.$store.dispatch(
      //   "user_site_server_features/update",
      //   _.merge(this.$route.params, {
      //     formData: this.getFormData(this.$el)
      //   })
      // );
    },
  },
  computed: {
    siteId() {
      return this.$route.params.site;
    },
    selectedServerFeatures: {
      get() {
        return this.$store.state.user.sites.servers.features.features;
      },
      set() {
        console.info("this setttttt");
      },
    },
  },
};
</script>
