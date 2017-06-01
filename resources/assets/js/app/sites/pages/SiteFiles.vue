<template>
    <div v-if="site">
        <framework-files v-if="site.framework"></framework-files>
        <site-custom-files></site-custom-files>
    </div>
</template>

<script>
    import {
        FrameworkFiles,
        SiteCustomFiles
    } from '../components'

    export default {
        components : {
            FrameworkFiles,
            SiteCustomFiles
        },
        created() {
            this.fetchData()
        },
        watch: {
            '$route': 'fetchData'
        },
        methods: {
            fetchData() {
                this.$store.dispatch('user_site_files/get', this.$route.params.site_id)
            }
        },
        computed: {
            site() {
                return this.$store.state.user_sites.site
            }
        }
    }
</script>