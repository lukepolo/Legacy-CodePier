<template>
    <modal v-if="lastReadAnnouncement && shouldShow">
        <div class="modal--header">
            <h1>CodePier was updated!</h1>
        </div>
        <div class="modal--body">
            <h2>New</h2>
            <ul>
                <li>Private IPS now are collected so we can improve security when opening ports.</li>
                <li>You can manage private IPs for servers in the server page</li>
                <li>New servers will take advantage of better NVM support</li>
                <li>DNS Lookups for wildcard SSL now perform on the name server your domain is using.</li>
                <li>Added new LTS node versions, and next version. (These will continue to roll following the releases of node)</li>
                <li>Added Vultr server features (IPV6, Private IPs, backups, DDOS protection)</li>
                <li>Easily see Private IP addresses from site overview pages</li>
                <li>We warn you if daemons / workers cannot be used in the sub nav of the site pages</li>
            </ul>

            <h2>Fixes</h2>
            <ul>
                <li>Fixed Postgres Database restart commands</li>
                <li>Fixed Postgres permission issues</li>
                <li>New Servers have improved performance tweaks</li>
                <li>SWAP now has a set of 2 GB, you can increase this still in the advanced options</li>
                <li>Load balancers wil now use private IPs of servers </li>
                <li>Firewall Rules will react better when adding a new server</li>
                <li>Worker / Daemons will not try to remove it knows the server does not have it installed</li>
                <li>And more!</li>
            </ul>

            <p class="text-center">
                Let us know what you think on <a target="_blank" href="https://twitter.com/codepier">twitter</a>!
            </p>
        </div>
        <div class="modal--footer">
            <span class="btn btn-primary" @click="markAnnouncementRead">OK Thanks!</span>
        </div>
    </modal>
</template>

<script>
export default {
  data() {
    return {
      announcementDate: moment("2018-11-01"),
    };
  },
  methods: {
    markAnnouncementRead() {
      this.$store.dispatch("system/markAnnouncementRead");
    },
  },
  computed: {
    shouldShow() {
      return this.announcementDate.isAfter(this.lastReadAnnouncement);
    },
    lastReadAnnouncement() {
      return moment(this.$store.state.user.user.last_read_announcement);
    },
  },
};
</script>
