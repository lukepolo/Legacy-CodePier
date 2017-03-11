<template>
    <div>
        <database-section :database="database" v-for="database in databases"></database-section>
    </div>
</template>

<script>
    import DatabaseSection from './Components/DatabaseSection.vue'
    export default {
        components:  {
            DatabaseSection
        },
        created() {
            this.fetchData()
        },
        watch: {
            '$route': 'fetchData'
        },
        methods: {
            fetchData() {
                if(this.siteId) {
                    this.$store.dispatch('getSiteSchemas', this.siteId)
                    this.$store.dispatch('getSiteServerFeatures', this.siteId)
                }

                if(this.serverId) {
                    this.$store.dispatch('getServerSchemas', this.serverId)
                    this.$store.dispatch('getServerFeatures', this.serverId)
                }
            }
        },
        computed: {
            siteId() {
                return this.$route.params.site_id
            },
            serverId() {
                return this.$route.params.server_id
            },
            databases() {

                let serverFeatures = {}

                if(this.siteId) {
                    serverFeatures = this.$store.state.siteServersFeaturesStore.site_server_features
                }

                if(this.serverId) {
                    serverFeatures = this.$store.state.serversStore.server_installed_features
                }

                if(_.has(serverFeatures, 'DatabaseService')) {
                    return _.keys(_.pickBy(serverFeatures.DatabaseService, function(options, database) {
                        if(database == 'MariaDB' || database == 'PostgreSQL' || database == 'MySQL') {
                            return options.enabled;
                        }
                    }))
                }
            }
        }
    }
</script>