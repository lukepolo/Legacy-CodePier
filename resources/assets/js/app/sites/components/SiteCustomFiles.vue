<template>
    <div>


        <div class="list">
            <div class="list--item list--item-accordion">
                custom/site/path <span class="icon-arrow-right"></span>
            </div>
            <div class="list--item list--item-accordion">
                custom/site/path <span class="icon-arrow-right"></span>
            </div>
            <div class="list--item list--item-accordion">
                custom/site/path <span class="icon-arrow-right"></span>
            </div>
        </div>

        <template v-if="site">
            <form @submit.prevent="addCustomFile" class="flex flex--baseline">
                <div class="flyform--group flex--grow">
                    <input type="text" name="file" v-model="form.file" placeholder=" ">
                    <label for="file">File Path</label>
                </div>

                <div class="flex--spacing">
                    <button class="btn btn-primary btn-small" type="submit">Add Custom File</button>
                </div>
            </form>

            <site-file :site="site" :file="file" v-for="file in customSiteFiles" :running="isRunningCommandFor(file)" :key="file"></site-file>
        </template>


    </div>
</template>

<script>
    import SiteFile from './SiteFile.vue';
    export default {
        data() {
            return {
                form : this.createForm({
                    file : ''
                })
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