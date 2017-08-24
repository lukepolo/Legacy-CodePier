<template>
    <form @submit.prevent="saveFile()">
        {{ filePath }}
        <div class="jcf-form-wrap">
            <a @click="reloadFile" class="btn btn-xs">Reload File</a>
        </div>

        <div ref="editor" v-file-editor class="editor"></div>

        <div class="btn-footer">
            <template v-if="running">
                {{ running.status }}
            </template>
            <button class="btn btn-primary" type="submit">Update File</button>
        </div>
    </form>
</template>

<script>
    export default {
        props: ['server', 'file', 'running'],
        created() {
            this.fetchData();
        },
        watch: {
            '$route': 'fetchData',
            'fileModel' : function() {
                if(_.isObject(this.fileModel)) {
                    let editor = this.$refs.editor;
                    ace.edit(editor).setValue(this.fileModel.unencrypted_content);
                    ace.edit(editor).clearSelection(1);
                }
            },
        },
        methods: {
            saveFile() {
                this.$store.dispatch('user_server_files/update', {
                    file: this.file,
                    server: this.server.id,
                    content: this.getContent(),
                    file_id: this.fileModel.id,
                });
            },
            fetchData() {
                if(!_.isObject(this.file)) {
                    this.$store.dispatch('user_server_files/find', {
                        custom : false,
                        file : this.file,
                        server : this.$route.params.server_id
                    })
                }
            },
            reloadFile() {
                this.$store.dispatch('user_server_files/reload', {
                    file : this.fileModel.id,
                    server : this.$route.params.server_id,
                })
            },
            getContent() {
                return ace.edit(this.$refs.editor).getValue();
            },
        },
        computed : {
            fileModel() {
                return _.find(this.serverFiles, (file) => {
                    return this.file === file.file_path;
                });
            },
            filePath() {
              if(_.isObject(this.file)) {
                  return this.file.file_path;
              }

              return this.file;
            },
            serverFiles() {
                return this.$store.state.user_server_files.files;
            }
        }
    }
</script>