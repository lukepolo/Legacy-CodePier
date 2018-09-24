<template>
   <div>
       <h3>DNS
           <tooltip message="Refresh DNS">
               <span class="icon-refresh2 text-link" @click="getDns(true)"></span>
           </tooltip>
       </h3>

       <template v-if="dns && dns.host">
           Your domain is currently pointing to :
           <a :href="'//' + dns.ip" target="_blank" :class="{ 'text-danger' : !dnsIsPointedToServer , 'text-success' : dnsIsPointedToServer}">
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
  methods: {
    getDns(refresh) {
      // let data = {
      //   site: this.$route.params.site_id,
      // };
      //
      // if (refresh) {
      //   data.refresh = true;
      // }
      //
      // this.$store.dispatch("user_site_dns/get", data);
    },
  },
  computed: {
    dns() {
      return null;
      // return this.$store.state.user.sites.site.dns // TDOO - should come back on the site?
    },
    dnsIsPointedToServer() {
      return false;
      // if (this.siteServers && this.dns.ip) {
      //   return _.indexOf(_.map(this.siteServers, "ip"), this.dns.ip) > -1;
      // }
    },
  },
};
</script>
