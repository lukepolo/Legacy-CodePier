<template>
    <div>
        <h3>Server Files</h3>
        <template v-if="possibleFiles && site">
            <site-file :site="site" :servers="site.servers" :file="file" v-for="file in possibleFiles" :running="isRunningCommandFor(file)"></site-file>
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
                this.$store.commit('SET_EDITABLE_SITE_FILES', []);
                this.$store.dispatch('getEditableFiles', this.$route.params.site_id);
            },
            isRunningCommandFor(file) {
                if(this.siteFiles) {
                    let foundFile =_.find(this.siteFiles, { file_path : file });
                    if(foundFile) {
                        return this.isCommandRunning('App\\Models\\Site\\SiteFile', foundFile.id);
                    }
                }

                return false;
            }
        },
        computed: {
            runningCommands() {
                return this.$store.state.serversStore.running_commands;
            },
            site() {
                return this.$store.state.sitesStore.site;
            },
            possibleFiles() {
                return this.$store.state.siteFilesStore.site_editable_files;
            },
            siteFiles() {
                return this.$store.state.siteFilesStore.site_files;
            }
        },
    }
</script>