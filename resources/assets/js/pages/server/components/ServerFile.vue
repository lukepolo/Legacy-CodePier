<template>
    <form @submit.prevent="saveFile()">
        {{ filePath }}
        <div class="jcf-form-wrap">
            <a @click="reloadFile" class="btn btn-xs">Reload File</a>
        </div>

        <div v-file-editor class="editor"></div>

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
        data() {
            return {
                file_model: null,
            }
        },
        watch: {
            'file_model.unencrypted_content'() {
                if(_.isObject(this.file_model)) {
                    let editor = $(this.$el).find('.editor')[0];
                    if(this.file_model.unencrypted_content) {
                        ace.edit(editor).setValue(this.file_model.unencrypted_content);
                        ace.edit(editor).clearSelection(1);
                    }
                }
            },
            watch: {
                '$route': 'fetchData'
            }
        },
        methods: {
            saveFile() {
                this.$store.dispatch('updateServerFile', {
                    file: this.file,
                    server: this.server.id,
                    content: this.getContent(),
                    file_id: this.file_model.id,
                });
            },
            fetchData() {
                this.file_model = this.file;

                if(!_.isObject(this.file_model)) {
                    this.file_model = _.find(this.serverFiles, (file) => {
                        return this.file_model ==  file.file_path;
                    });

                    if(!this.file_model) {
                        this.$store.dispatch('findServerFile', {
                            custom : false,
                            file : this.file,
                            server : this.$route.params.server_id
                        }).then((file) => {
                            this.file_model = file;
                        });
                    }
                }
            },
            reloadFile() {
                this.$store.dispatch('reloadServerFile', {
                    file : this.file_model.id,
                    server : this.$route.params.server_id,
                })
            },
            getContent() {
                return ace.edit($(this.$el).find('.editor')[0]).getValue();
            },
        },
        computed : {
            filePath() {
              if(_.isObject(this.file)) {
                  return this.file.file_path;
              }

              return this.file;
            },
            serverFiles() {
                return this.$store.state.serverFilesStore.server_files;
            }
        }
    }
</script>