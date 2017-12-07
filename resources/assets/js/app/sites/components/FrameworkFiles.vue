<template>
    <div class="list">
        <template v-if="possibleFiles">
            <site-file :forceShow="index === 0 && possibleFiles.length === 1" :site="site" :file="file" v-for="(file, index) in possibleFiles" :key="file" :running="isRunningCommandFor(file)"></site-file>
        </template>
    </div>
</template>

<script>
    import SiteFile from './SiteFile.vue';
    export default {
        components : {
          SiteFile
        },
        created() {
            this.fetchData();
        },
        methods: {
            isRunningCommandFor(file) {
                if(this.siteFiles) {
                    let foundFile =_.find(this.siteFiles, { file_path : file });
                    if(foundFile) {
                        return this.isCommandRunning('App\\Models\\File', foundFile.id);
                    }
                }

                return false;
            }
        },
        computed: {
            site() {
                return this.$store.state.user_sites.site;
            },
            siteFiles() {
                return this.$store.state.user_site_files.files;
            }
        },
    }
</script>