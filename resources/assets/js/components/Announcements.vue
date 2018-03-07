<template>
    <modal v-if="lastReadAnnouncement && shouldShow">
        <div class="modal--header">
            <h1>CodePier was updated!</h1>
        </div>
        <div class="modal--body">
            <h2>New</h2>
            <ul>
                <li>Favicon now changes when there are running commands</li>
                <li>Server Providers are named : this gives you the flexibility to add multiple accounts of the same provider</li>
                <li>Deployments now allow you to set some tasks to be run after all deployments are successful!</li>
                <li>You can now change a site to a wildcard site on the site overview page</li>
            </ul>

            <h3>Language Specific</h3>
            <h4>PHP</h4>
            <ul>
                <li>Moved "Generic PHP" to modern PHP , then added Legacy PHP option</li>
            </ul>

            <h2>Changed</h2>
            <ul>
                <li>Custom Provision script has been updated, and now checks which system your trying to provision and will fail if you don't have the correct system</li>
                <li>Moved the get help in the nav bar to a first class citizen!</li>
                <li>Removed oauth by digital ocean in favor of API tokens to make it uniform with the other providers and this allows you to know which account your Digital Ocean is linked to.</li>
            </ul>

            <h2>Fixed</h2>
            <ul>
                <li>Long Server / Site names have been fixed</li>
                <li>Cron Job Maker has been improved to have the look and feel of the rest of the site</li>
                <li>Multiple sites of the same domain / name that would cause conflicts</li>
                <li>Fixed total calculations on multiple servers when deployments are completed</li>
                <li>Docked with CodePier image has been restored</li>
                <li>Multiple areas of the site had UI bugs have been fixed</li>
            </ul>


            As always we have some things that are in the backend that cannot be seen, but just know that we are working to incorporate bigger things very soon!

            Sneak Peak :

            <ul>
                <li>Teams - 3-4 weeks</li>
                <li>Bitts - 2 weeks</li>
                <li>Buoy Marketplace Overhaul 2 weeks</li>
                <li>Free Database Backups - 4-6 weeks</li>
            </ul>

            Those are the bigger things taht are coming down the line, of course we will have other features we will be working on!

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
      announcementDate: moment("2018-03-07"),
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
