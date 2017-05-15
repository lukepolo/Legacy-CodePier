<template>
    <div>
        <h3>Custom Files</h3>
        <template v-if="server">
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

            <server-file :server="server" :file="file" v-for="file in customServerFiles" :key="file" :running="isRunningCommandFor(file)"></server-file>
        </template>
    </div>
</template>

<script>
    import ServerFile from './ServerFile.vue';
    export default {
        components : {
            ServerFile,
        },
        data() {
            return {
                form : {
                    file : ''
                }
            }
        },
        methods: {
            isRunningCommandFor(file) {
                if(this.serverFiles) {
                    let foundFile =_.find(this.serverFiles, { file_path : file });
                    if(foundFile) {
                        return this.isCommandRunning('App\\Models\\File', foundFile.id);
                    }
                }

                return false;
            },
            addCustomFile() {
                this.$store.dispatch('findServerFile', {
                    custom : true,
                    file : this.form.file,
                    server : this.$route.params.server_id
                }).then(() => {
                    this.form.file = '';
                });
            },
        },
        computed: {
            server() {
                return this.$store.state.user_servers.server;
            },
            serverFiles() {
                return this.$store.state.serverFilesStore.server_files;
            },
            runningCommands() {
                return this.$store.state.serversStore.running_commands;
            },
            customServerFiles() {
                return _.filter(this.serverFiles, function(file) {
                    return file.custom;
                });
            }
        }
    }
</script>