<template>
    <section id="right">
        <p v-for="server in servers">
            {{ server.name }} - {{ server.ssh_connection }}
            <p>4 / 40 GB</p>
            <p>1.2 Avg Load</p>
            <div class="dropdown">
                <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                    Actions
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                    <li><a href="#">Restart Web Services</a></li>
                    <li><a href="#">Restart Server</a></li>
                    <li><a href="#">Restart Database</a></li>
                    <li><a href="#">Restart Workers</a></li>
                    <li role="separator" class="divider"></li>
                    <li><a href="#">Archive Server</a></li>
                </ul>
            </div>
        </p>
    </section>
</template>

<script>
    export default {
        created() {
            this.fetchData();
        },
        watch: {
            '$route': 'fetchData'
        },
        methods: {
            fetchData: function () {
                siteStore.dispatch('getSiteServers', this.$route.params.site_id);
            },
        },
        computed : {
            servers : () => {
                return siteStore.state.site_servers;
            }
        }
    }
</script>