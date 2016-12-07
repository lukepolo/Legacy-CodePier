<template>
    <div>
        <h3>Framework Files</h3>
        <template v-if="files && site">
            <site-file :site="site" :servers="site.servers" :file="file" v-for="file in files"></site-file>
        </template>
    </div>
</template>

<script>
    import SiteFile from './../../../components/SiteFile.vue';
    export default {
        components : {
          SiteFile
        },
        created() {
            this.fetchData();
        },
        watch: {
            '$route': 'fetchData'
        },
        methods: {
            fetchData() {
                this.$store.commit('SET_EDITABLE_FRAMEWORK_FILES', []);
                this.$store.dispatch('getSite', this.$route.params.site_id);
                this.$store.dispatch('getEditableFrameworkFiles', this.$route.params.site_id);
            }
        },
        computed: {
            site() {
                return this.$store.state.sitesStore.site;
            },
            files() {
                return this.$store.state.serversStore.editable_framework_files;
            }
        },
    }
</script>