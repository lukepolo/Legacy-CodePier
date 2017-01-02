<template>
    <div>
        <h3>Custom Files</h3>
        <template v-if="site">
            <site-file :site="site" :servers="site.servers" :file="file" v-for="file in siteFiles" :running="isRunningCommandFor(file)"></site-file>
        </template>
    </div>
</template>

<script>
    import SiteFile from './../../../components/SiteFile.vue';
    export default {
        components : {
          SiteFile
        },
        methods: {
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
            siteFiles() {
                return _.filter(this.$store.state.sitesStore.site_files, function(file) {
                    return file.custom;
                });
            }
        },
    }
</script>