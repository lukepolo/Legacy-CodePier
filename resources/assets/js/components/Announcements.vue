<template>
  <modal v-if="lastReadAnnouncement && shouldShow">
    <div class="modal--header"><h1>CodePier was updated!</h1></div>
    <div class="modal--body">
      <h2>New</h2>
      <ul>
        <li>Ubuntu 18.04 Servers Now Available</li>
        <li>New selector for choosing which server version you wish to use</li>
      </ul>

      <h2>Fixes</h2>
      <ul>
        <li>
          Progress now starts at 1% to give better feedback something is
          happening
        </li>
        <li>Fixed issues with Swift Installation</li>
        <li>Other Minor Bug Fixes</li>
      </ul>

      <p class="text-center">
        Let us know what you think on
        <a target="_blank" href="https://twitter.com/codepier">twitter</a>!
      </p>
    </div>
    <div class="modal--footer">
      <span class="btn btn-primary" @click="markAnnouncementRead"
        >OK Thanks!</span
      >
    </div>
  </modal>
</template>

<script>
export default {
  data() {
    return {
      announcementDate: moment("2019-01-02"),
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
