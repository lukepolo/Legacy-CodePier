<template>
    <div class="list">
        <template v-if="siteFiles">
            <site-file :forceShow="siteFiles.length === 1" :site="site" :file="file" v-for="file in files" :key="file.id" :running="isRunningCommandFor(file)"></site-file>
        </template>
    </div>
</template>

<script>
    import SiteFile from './SiteFile.vue';
    export default {
        components : {
          SiteFile
        },
        methods: {
            isRunningCommandFor(file) {
                if(this.files) {
                    let foundFile =_.find(this.files, { id : file.id });
                    if(foundFile) {
                        return this.isCommandRunning('App\\Models\\File', file.id);
                    }
                }

                return false;
            }
        },
        computed: {
            site() {
                return this.$store.state.user_sites.site;
            },
            files() {
                return this.siteFiles.filter((file) => {
                    return file.framework_file;
                });
            },
            siteFiles() {
                return this.$store.state.user_site_files.files
            }
        },
    }
</script>