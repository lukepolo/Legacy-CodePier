<template>
    <div>
        <h3>Server Files</h3>
        <template v-if="possibleFiles && server">
            <server-file :server="server" :file="file" v-for="file in possibleFiles" :running="isRunningCommandFor(file)"></server-file>
        </template>
    </div>
</template>

<script>
    import ServerFile from './components/ServerFile.vue';
    export default {
        components : {
            ServerFile
        },
        created() {
            this.fetchData();
        },
        watch: {
            '$route': 'fetchData'
        },
        methods: {
            fetchData() {
                this.$store.dispatch('getServer', this.$route.params.server_id)
                this.$store.dispatch('getServerFiles', this.$route.params.server_id)
                this.$store.dispatch('getEditableServerFiles', this.$route.params.server_id)
            },
            isRunningCommandFor(file) {
//                if(this.siteFiles) {
//                    let foundFile =_.find(this.siteFiles, { file_path : file });
//                    if(foundFile) {
//                        return this.isCommandRunning('App\\Models\\File', foundFile.id);
//                    }
//                }
//
                return false;
            }
        },
        computed: {
            server() {
                return this.$store.state.serversStore.server
            },
            serverFiles() {
                return this.$store.state.serverFilesStore.server_files
            },
            runningCommands() {
                return this.$store.state.serverFilesStore.running_commands
            },
            possibleFiles() {
                return this.$store.state.serverFilesStore.server_editable_files
            }
        },
    }
</script>