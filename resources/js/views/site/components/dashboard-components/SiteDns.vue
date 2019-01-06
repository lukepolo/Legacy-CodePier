<template>
  <div>
    <h3>
      DNS
      <tooltip message="Refresh DNS">
        <span class="icon-refresh2 text-link" @click="getDns"></span>
      </tooltip>
    </h3>

    <template v-if="dns && dns.host">
      Your domain is currently pointing to :
      <a
        :href="'//' + dns.ip"
        target="_blank"
        :class="{
          'text-danger': !dnsIsPointedToServer,
          'text-success': dnsIsPointedToServer,
        }"
      >
        {{ dns.ip }}
      </a>
    </template>
    <template v-else>
      Cannot find DNS entry
    </template>
  </div>
</template>

<script>
export default {
  props: ["site"],
  data() {
    return {
      dns: null,
    };
  },
  watch: {
    site: {
      immediate: true,
      handler: "getDns",
    },
  },
  methods: {
    getDns() {
      this.$store.dispatch("user/sites/getDns", this.site.id).then((dns) => {
        this.dns = dns;
      });
    },
  },
  computed: {
    dnsIsPointedToServer() {
      // TODO
      return false;
      // if (this.siteServers && this.dns.ip) {
      //   return _.indexOf(_.map(this.siteServers, "ip"), this.dns.ip) > -1;
      // }
    },
  },
};
</script>
