<template>
    <div v-if="site">
        <database-section :database="database" v-for="database in databases"></database-section>
    </div>
</template>

<script>
    import DatabaseSection from './components/DatabaseSection.vue'
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
                this.$store.dispatch('getSite', this.$route.params.site_id)
                this.$store.dispatch('getSiteServerFeatures', this.$route.params.site_id)
            }
        },
        computed: {
            site() {
                return this.$store.state.sitesStore.site
            },
            databases() {
                let siteServerFeatures = this.$store.state.siteServersFeaturesStore.site_server_features
                if(_.has(siteServerFeatures, 'DatabaseService')) {
                    return _.keys(_.pickBy(siteServerFeatures.DatabaseService, function(options, database) {
                        if(database == 'MariaDB' || database == 'PostgreSQL' || database == 'MySQL') {
                            return options.enabled;
                        }
                    }))
                }
            }
        }
    }
</script>