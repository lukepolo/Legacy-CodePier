<template>
    <section>
        <left-nav></left-nav>
        <section id="middle" class="section-column">
            <site-header></site-header>
            <div class="section-content">
                <div class="container">
                    <site-nav></site-nav>
                    <template v-if="files && site">
                        <site-file :site="site" :servers="site.servers" :file="file" v-for="file in files"></site-file>
                    </template>
                </div>
            </div>
        </section>
        <servers></servers>
    </section>
</template>

<script>
    import LeftNav from './../../core/LeftNav.vue';
    import SiteNav from './components/SiteNav.vue';
    import Servers from './components/Servers.vue';
    import SiteHeader from './components/SiteHeader.vue';
    import SiteFile from './../../components/SiteFile.vue';
    export default {
        components: {
            SiteNav,
            LeftNav,
            Servers,
            SiteFile,
            SiteHeader,
        },
        created() {
            this.fetchData();
        },
        watch: {
            '$route': 'fetchData'
        },
        methods: {
            fetchData: function () {
                this.$store.dispatch('getSite', this.$route.params.site_id);
                this.$store.dispatch('getEditableFrameworkFiles', this.$route.params.site_id);
            }
        },
        computed: {
            site: () => {
                return siteStore.state.site;
            },
            files() {
                return this.$store.state.serversStoreeditable_framework_files;
            }
        },
    }
</script>