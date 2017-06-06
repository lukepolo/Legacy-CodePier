<template>
    <section>
        <template v-if="possibleFiles && site">
            <site-file :site="site" :file="file" v-for="file in possibleFiles" :key="file" :running="isRunningCommandFor(file)"></site-file>
        </template>
    </section>
</template>

<script>

    import {
        SiteFile,
    } from '../components';

    export default {
        components : {
            SiteFile,
        },
        created() {
            this.fetchData();
        },
        watch: {
            '$route': 'fetchData'
        },
        methods: {
            fetchData() {
                this.$store.dispatch('user_site_files/getEditableFiles', this.$route.params.site_id);
            },
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
            runningCommands() {
                return this.$store.state.commands.running_commands;
            },
            site() {
                return this.$store.state.user_sites.site;
            },
            possibleFiles() {
                return this.$store.state.user_site_files.editable_files;
            },
            siteFiles() {
                return this.$store.state.user_site_files.site_files;
            }
        },
    }
</script>