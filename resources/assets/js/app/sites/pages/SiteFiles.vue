<template>
    <div v-if="site">
        <h3 class="flex--grow">
            Framework Files
        </h3>
        <framework-files v-if="site.framework"></framework-files>
        <custom-files></custom-files>
    </div>
</template>

<script>
    import FrameworkFiles from './../components/FrameworkFiles'
    import CustomFiles from './../../setup/components/CustomFiles'

    export default {
        components : {
            CustomFiles,
            FrameworkFiles,
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