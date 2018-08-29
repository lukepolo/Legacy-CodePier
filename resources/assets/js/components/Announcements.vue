<template>
    <modal v-if="lastReadAnnouncement && shouldShow">
        <div class="modal--header">
            <h1>CodePier was updated!</h1>
        </div>
        <div class="modal--body">
            <h2>New</h2>
            <ul>
                <li>New Language is in beta! Swift , specially Vapor!</li>
            </ul>

            <h2>Changed</h2>
            <ul>
                <li>Lots has changed, but no worries it was almost all backend</li>
            </ul>

            <h2>Fixes</h2>
            <ul>
                <li>Worker Directories not working correctly</li>
                <li>Performance enhancements (Server Creation now takes significantly less time)</li>
            </ul>

            <h3>Language Specific</h3>
            <h4>Vapor (Swift)</h4>
            <ul>
                <li>Launched into beta, let us know what we missed!</li>
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
      announcementDate: moment("2018-08-29"),
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
