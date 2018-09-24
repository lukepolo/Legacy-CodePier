<template>
    <router-link :to="{ name : 'site_backups', params : { site : this.site.id } }" tag="div" class="grid--item">
        <div class="providers--item">
            <div class="providers--item-header">
                <div class="providers--item-icon">
                    <span class="icon-database"></span>
                </div>
            </div>
            <div class="providers--item-footer">
                <div class="providers--item-footer-connect">
                    <h4>Database Backups</h4>
                </div>
            </div>
        </div>
    </router-link>
</template>

<script>
export default {
  props: ["site"],
};
</script>
