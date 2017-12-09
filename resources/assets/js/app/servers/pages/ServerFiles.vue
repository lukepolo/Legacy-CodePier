<template>
    <section>
        <template v-if="files && server">
            <server-file :server="server" :file="file" v-for="file in files" :key="file" :running="isRunningCommandFor(file)"></server-file>
        </template>
        <custom-files></custom-files>
    </section>
</template>

<script>
    import ServerFile from '../components/ServerFile.vue';
    import CustomFiles from '../components/ServerCustomFiles.vue';
    export default {
        components : {
            ServerFile,
            CustomFiles
        },
        created() {
            this.fetchData();
        },
        watch: {
            '$route': 'fetchData'
        },
        methods: {
            fetchData() {
                this.$store.dispatch('user_server_files/get', this.$route.params.server_id)
            },
            isRunningCommandFor(file) {
                if(this.serverFiles) {
                    let foundFile =_.find(this.serverFiles, { file : file.id });
                    if(foundFile) {
                        return this.isCommandRunning('App\\Models\\File', file.id);
                    }
                }
                return false;
            }
        },
        computed: {
            files() {
                return this.serverFiles.filter((file) => {
                  return (!file.custom && !file.framework_file)
                });
            },
            server() {
                return this.$store.state.user_servers.server
            },
            serverFiles() {
                return this.$store.state.user_server_files.files
            },
            runningCommands() {
                return this.$store.state.commands.running_commands
            },
        },
    }
</script>