<template>
    <section>
        <custom-files></custom-files>
        <template v-if="possibleFiles && server">
            <server-file :server="server" :file="file" v-for="file in possibleFiles" :key="file" :running="isRunningCommandFor(file)"></server-file>
        </template>
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
                this.$store.dispatch('user_server_files/getEditableFiles', this.$route.params.server_id)
            },
            isRunningCommandFor(file) {
                if(this.serverFiles) {
                    let foundFile =_.find(this.serverFiles, { file_path : file });
                    if(foundFile) {
                        return this.isCommandRunning('App\\Models\\File', foundFile.id);
                    }
                }
                return false;
            }
        },
        computed: {
            server() {
                return this.$store.state.user_servers.server
            },
            serverFiles() {
                return this.$store.state.user_server_files.files
            },
            runningCommands() {
                return this.$store.state.commands.running_commands
            },
            possibleFiles() {
                return this.$store.state.user_server_files.editable_files
            }
        },
    }
</script>