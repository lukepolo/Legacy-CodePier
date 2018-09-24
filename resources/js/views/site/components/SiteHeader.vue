<template>
    <div>
        <notification-channels-form :site="site"></notification-channels-form>
        <site-rename-form :site="site"></site-rename-form>
        <delete-site :site="site"></delete-site>
        <router-link :class="{ 'btn-disabled' : !siteActionsEnabled }" class="btn btn-primary" :to="{ name: 'site_repository', params : { site_id : site.id } }">Manage Site &nbsp;<span class="icon-arrow-right"></span> </router-link>
    </div>
</template>

<script>
import DeleteSite from "./DeleteSite";
import SiteRenameForm from "./SiteRenameForm";
import NotificationChannelsForm from "./NotificationChannelsForm";

export default {
  components: { DeleteSite, SiteRenameForm, NotificationChannelsForm },
  props: {
    site: {
      required: true,
    },
  },
};
</script>
