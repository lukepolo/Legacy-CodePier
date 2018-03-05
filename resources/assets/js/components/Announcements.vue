<template>
    <modal v-if="lastReadAnnouncement && shouldShow">
        <div class="modal--header">
            <h1>CodePier was updated!</h1>
        </div>
        <div class="modal--body">
            <h2>New</h2>
            <ul>
                <li>Announcements will show up in these modals!</li>
                <li>Open events bar into a new window for better accessibility</li>
                <li>New modal to help new users with the experience of CodePier</li>
                <li>New Server Provisioning Events so you can watch in your events bar while you provision a new server</li>
                <li>Added some timings to deployments and provisions events</li>
            </ul>

            <h3>Language Specific</h3>
            <h4>PHP</h4>
            <ul>
                <li>OpCache configuration is now under Language Settings</li>
            </ul>

            <h2>Changed</h2>
            <ul>
                <li>Support is now in the “gear” menu</li>
                <li>Piles has a new location to make it known what piles do</li>
                <li>Cleaned up reveal sudo / mysql passwords</li>
                <li>Creating a server from the severs page now makes you confirm with warning</li>
                <li>Roll backs now just release the old release without any other commands running , unless the old release is missing</li>
            </ul>

            <h3>Language Specific</h3>
            <h4>PHP</h4>
            <ul>
                <li>Composer is now installed via getcomposer.org/installer</li>
            </ul>

            <h2>Fixed</h2>
            <ul>
                <li>Notifications saying you're not connected with your account</li>
                <li>Fixed sudo password reset</li>
                <li>Fixed issue of failed deployment emails not respecting breaking lines</li>
                <li>Fixed Service restart groups</li>
                <li>You can now delete a site before fully configured</li>
                <li>Auto removal of packages after updating the system</li>
                <li>Sites now show the attached servers when making server changes</li>
                <li>Server features now will block feature installs while there is another install running</li>
                <li>Force install of UFW, if for some reason its not installed already</li>
            </ul>

            <h3>Language Specific</h3>
            <h4>PHP</h4>
            <ul>
                <li>The composer update cron job is not attached to the server</li>
            </ul>

            <h5>PHP - Laravel</h5>
            <ul>
                <li>Changed the default order of clearing caches for deployments</li>
            </ul>

            There were a lot of other fixes and performance enhancements, but these are the most notable!
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
