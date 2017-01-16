<template>
    <div v-if="site">
        <server-files></server-files>
    </div>
</template>

<script>
    import ServerFiles from  './components/SiteServerFiles.vue'
    export default {
        components : {
            ServerFiles
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
                this.$store.dispatch('getSite', this.$route.params.site_id)
                this.$store.dispatch('getSiteFiles', this.$route.params.site_id)
            }
        },
        computed: {
            site() {
                return this.$store.state.sitesStore.site
            }
        }
    }
</script>