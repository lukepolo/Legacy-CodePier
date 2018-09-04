<template>
    <modal v-if="lastReadAnnouncement && shouldShow">
        <div class="modal--header">
            <h1>CodePier was updated!</h1>
        </div>
        <div class="modal--body">
            <h2>New</h2>
            <ul>
                <h4>Vapor for Swift!</h4>
                <li>We have released our initial take on using Vapor! Give it a go and let us know if we missed anything.</li>
                <li>Discord Notification Provider! You can now use discord for your notifications!</li>
                <li>For subscribers we now check if they have an invalid / expiring SSL certificate daily! </li>
            </ul>

            <h2>Fixes</h2>
            <ul>
                <li>A Ton Of Bug Fixes</li>
                <li>Server provisioning should see a huge performance increase</li>
                <li>Fixed issues of installing some server features</li>
                <li>We always clear our DNS cache to check for wild card SSL CNAME checks</li>
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
      announcementDate: moment("2018-09-01"),
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
