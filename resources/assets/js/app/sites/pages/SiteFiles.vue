<template>
    <div v-if="site">
        <framework-files v-if="site.framework"></framework-files>
    </div>
</template>

<script>
    import {
        FrameworkFiles
    } from '../components'

    export default {
        components : {
            FrameworkFiles
        },
        created() {
            this.fetchData()
        },
        watch: {
            '$route': 'fetchData'
        },
        methods: {
            fetchData() {
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