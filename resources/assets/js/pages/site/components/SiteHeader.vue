<template>
    <h3 class="section-header primary" v-if="site">
        <back></back>
        {{ site.name }}
        <div class="pull-right">
            <div class="dropdown">
                <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                    <i class="fa fa-server"></i>
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <confirm dispatch="restartSiteWebServices"><a href="#">Restart Web Services</a></confirm>
                    </li>
                    <li>
                        <confirm dispatch="restartSiteServers"><a href="#">Restart Servers</a></confirm>
                    </li>
                    <li>
                        <confirm dispatch="restartSiteDatabases"><a href="#">Restart Databases</a></confirm>
                    </li>
                    <li>
                        <confirm dispatch="restartSiteWorkers"><a href="#">Restart Workers</a></confirm>
                    </li>
                </ul>
            </div>
        </div>
    </h3>
</template>

<script>
    export default {
        computed: {
            site() {
                return this.$store.state.sitesStore.site;
            }
        }
    }
</script>