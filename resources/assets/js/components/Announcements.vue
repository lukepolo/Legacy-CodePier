<template>
    <modal v-if="lastReadAnnouncement && shouldShow">
        <div class="modal--header">
            <h1>CodePier was updated!</h1>
        </div>
        <div class="modal--body">
            Here is a quick run down of what was released :
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
      announcementDate: moment("2018-02-18 00:00:00")
    };
  },
  methods: {
    markAnnouncementRead() {
      this.$store.dispatch("system/markAnnouncementRead");
    }
  },
  computed: {
    shouldShow() {
      return this.announcementDate.isAfter(this.lastReadAnnouncement);
    },
    lastReadAnnouncement() {
      return moment(this.$store.state.user.user.last_read_announcement);
    }
  }
};
</script>
