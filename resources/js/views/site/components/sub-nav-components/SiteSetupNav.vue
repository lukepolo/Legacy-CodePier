<template>
  <div class="tab-container tab-left">
    <ul class="nav nav-tabs">
      <router-link
        :to="{ name: 'site.repository', params: { site: siteId } }"
        tag="li"
        exact
      >
        <a>
          Repository
          <div class="small">Your app's information</div>
        </a>
      </router-link>

      <router-link
        :to="{ name: 'site.deployment', params: { site: siteId } }"
        tag="li"
      >
        <a>
          Deployment
          <div class="small">Customize your app's deployment</div>
        </a>
      </router-link>

      <router-link
        :to="{ name: 'site.files', params: { site: siteId } }"
        tag="li"
      >
        <a>
          Site Files
          <div class="small">
            Your app has some default files that need to be configured
          </div>
        </a>
      </router-link>

      <router-link
        :to="{ name: 'site.databases', params: { site: siteId } }"
        tag="li"
        v-if="site"
      >
        <a>
          Databases
          <div class="small">Setup your site's databases and users</div>
        </a>
      </router-link>
    </ul>

    <div class="tab-content">
      <div role="tabpanel" class="tab-pane active"><slot></slot></div>
    </div>
  </div>
</template>
<script>
export default {
  watch: {
    $route: function() {
      $("#middle .section-content").scrollTop(0);
    },
  },
  computed: {
    siteId() {
      return this.$route.params.site;
    },
    site() {
      return this.$store.getters["user/sites/show"](this.siteId);
    },
  },
};
</script>
