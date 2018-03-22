<template>
    <modal v-if="lastReadAnnouncement && shouldShow">
        <div class="modal--header">
            <h1>CodePier was updated!</h1>
        </div>
        <div class="modal--body">
            <h2>New</h2>
            <ul>
                <li>Wildcard Certificates Available with automatic renewals for all DNS providers! Was no easy feat!</li>
                <li>Bitts are now available which are little code snippets that you can use to run on your server. There are future improvements to this system incoming over the next several releases</li>
            </ul>

            <h2>Changed</h2>
            <ul>
                <li>Let's Encrypt Package is replaced by cert-bot, you will not need to do anything</li>
                <li>SSL Certificates screen has been adjusted to a new UI workflow, these improvements will be coming to other areas of the app in the next several releases</li>
            </ul>

            <h2>Fixes</h2>
            <ul>
                <li>When updating language settings, we now invoke to refresh all server files so we can make certain they are in sync with UI.</li>
                <li>Switching between frameworks now replaces cronjobs and server files and framework files properly</li>
                <li>Misc backend fixes to make these a bit smoother for the user.</li>
            </ul>

            <h3>Language Specific</h3>
            <h4>PHP</h4>
            <ul>
                <li>Fixed NGINX config containing `internal` directive for apps that need the use of it</li>
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
      announcementDate: moment("2018-03-21"),
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
