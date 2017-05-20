<template>
    <div>
        <h3>Custom Files</h3>
        <template v-if="site">
            <div class="jcf-form-wrap">
                <form @submit.prevent="addCustomFile" class="floating-labels">
                    <div class="jcf-input-group">
                        <input type="text" name="file" v-model="form.file">
                        <label for="file">
                            <span class="float-label">File</span>
                        </label>
                    </div>

                    <div class="btn-footer">
                        <button class="btn btn-primary" type="submit">Add Custom File</button>
                    </div>
                </form>
            </div>

            <site-file :site="site" :file="file" v-for="file in customSiteFiles" :running="isRunningCommandFor(file)" :key="file"></site-file>
        </template>
    </div>
</template>

<script>
    import SiteFile from './SiteFile.vue';
    export default {
        data() {
            return {
                form : {
                    file : ''
                }
            }
        },
        components : {
          SiteFile
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
            },
            addCustomFile() {
                this.$store.dispatch('user_site_files/find', {
                    custom : true,
                    file : this.form.file,
                    site : this.$route.params.site_id
                }).then(() => {
                    this.form.file = '';
                });
            },
        },
        computed: {
            runningCommands() {
                return this.$store.state.commands.running_commands;
            },
            site() {
                return this.$store.state.user_sites.site;
            },
            customSiteFiles() {
                return _.filter(this.$store.state.user_site_files.files, function(file) {
                    return file.custom;
                });
            }
        },
    }
</script>