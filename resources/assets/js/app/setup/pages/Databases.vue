<template>
    <section>
        <template v-if="databases && databases.length">
            <database-section :database="database" v-for="database in databases" :key="database.id"></database-section>
        </template>
        <template v-else>
            <p>Currently you do not have any databases installed on this system.</p>
        </template>
    </section>
</template>

<script>
    import DatabaseSection from '../components/DatabaseSection'
    export default {
        components:  {
            DatabaseSection,
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
                    this.$store.dispatch('user_site_schemas/get', this.siteId)
                    this.$store.dispatch('user_site_schema_users/get', this.siteId)
                    this.$store.dispatch('user_site_server_features/get', {
                        site : this.siteId
                    })
                }

                if(this.serverId) {
                    this.$store.dispatch('user_server_schemas/get', this.serverId)
                    this.$store.dispatch('user_server_schema_users/get', this.serverId)
                    this.$store.dispatch('user_server_features/get', this.serverId)
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
                    serverFeatures = this.$store.state.user_site_server_features.features
                }

                if(this.serverId) {
                    serverFeatures = this.$store.state.user_server_features.features
                }

                if(_.has(serverFeatures, 'DatabaseService')) {
                    return _.keys(_.pickBy(serverFeatures.DatabaseService, function(options, database) {
                        if(database === 'MariaDB' || database === 'PostgreSQL' || database === 'MySQL') {
                            return options.enabled;
                        }
                    }))
                }
            }
        }
    }
</script>