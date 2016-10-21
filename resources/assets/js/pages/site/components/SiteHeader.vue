<template>
    <h3 class="section-header primary" v-if="site">
        {{ site.name }}
        <template v-if="site_servers">
            <div class="pull-right">
                <div class="dropdown">
                    <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                        <i class="fa fa-server"></i>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                        <li><a href="#">Restart Web Services</a></li>
                        <li><a href="#">Restart Servers</a></li>
                        <li><a href="#">Restart Databases</a></li>
                        <li><a href="#">Restart Workers</a></li>
                    </ul>
                </div>
            </div>
        </template>
    </h3>
</template>

<script>
    export default {
        computed: {
            site() {
                return this.$store.state.sitesStore.site;
            },
            site_servers() {
                return [];
            }
        }
    }
</script>