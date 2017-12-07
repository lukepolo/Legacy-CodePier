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
        },
        watch: {
            '$route': 'fetchData'
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
            runningCommands() {
                return this.$store.state.commands.running_commands;
            },
            site() {
                return this.$store.state.user_sites.site;
            },
            siteFiles() {
                return this.$store.state.user_site_files.files;
            }
        },
    }
</script>