<template>
    <modal v-if="lastReadAnnouncement && shouldShow">
        <div class="modal--header">
            <h1>CodePier was updated!</h1>
        </div>
        <div class="modal--body">
            <h2>New</h2>
            <ul>

            </ul>

            <h3>Language Specific</h3>
            <h4>PHP</h4>
            <ul>
            </ul>

            <h2>Changed</h2>
            <ul>
            </ul>

            <h3>Language Specific</h3>
            <h4>PHP</h4>
            <ul>
                <li>Composer is now installed via getcomposer.org/installer</li>
            </ul>

            <h2>Fixed</h2>
            <ul>

            </ul>

            <h3>Language Specific</h3>
            <h4>PHP</h4>
            <ul>
            </ul>

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
      announcementDate: moment("2018-02-18"),
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
