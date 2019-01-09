<template>
  <div class="tab-container tab-left">
    <ul class="nav nav-tabs">
      <router-link
        :to="{ name: 'site.ssh-keys', params: { site: siteId } }"
        tag="li"
        class="wizard-item"
        exact
      >
        <a>
          SSH Keys
          <div class="small">
            Add ssh keys that are required to access the server
          </div>
        </a>
      </router-link>

      <router-link
        :to="{ name: 'site.firewall-rules', params: { site: siteId } }"
        tag="li"
        class="wizard-item"
      >
        <a>
          Firewall Rules
          <div class="small">Setup your apps firewall rules</div>
        </a>
      </router-link>

      <router-link
        :to="{ name: 'site.ssl-certificates', params: { site: siteId } }"
        tag="li"
        v-if="site && site.domain !== 'default'"
      >
        <a>
          SSL Certificates
          <div class="small">Configure SSL certificates for your app</div>
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
  computed: {
    siteId() {
      return this.$route.params.site;
    },
    site() {
      return this.$store.getters["user/sites/show"](this.siteId);
    },
  },
  watch: {
    $route: function() {
      // $("#middle .section-content").scrollTop(0);
    },
  },
};
</script>
