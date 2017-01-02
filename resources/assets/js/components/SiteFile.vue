<template>
    <form @submit.prevent="saveFile()">
        {{ filePath }}
        <div v-file-editor class="editor"></div>
        <server-selector :servers="servers" :param="selected_servers"></server-selector>
        <div class="btn-footer">
            <template v-if="running">
                {{ running.status }}
            </template>
            <button class="btn btn-primary" type="submit">Update File</button>
        </div>
    </form>
</template>

<script>
    import ServerSelector from './ServerSelector.vue';
    export default {
        props: ['site', 'servers', 'file', 'running'],
        components: {
            ServerSelector
        },
        created() {
            this.fetchData();
        },
        data() {
            return {
                file_model: null,
                selected_servers: _.map(this.site.servers, 'id'),
            }
        },
        watch: {
            'file_model'() {
                if(_.isObject(this.file_model)) {
                    let editor = $(this.$el).find('.editor')[0];
                    ace.edit(editor).setValue(this.file_model.unencrypted_content);
                    ace.edit(editor).clearSelection(1);
                }

            },
            watch: {
                '$route': 'fetchData'
            }
        },
        methods: {
            fetchData() {

                this.file_model = this.file;

                if(!_.isObject(this.file_model)) {
                    this.file_model = _.find(this.siteFiles, (file) => {
                        return this.file_model ==  file.file_path;
                    });

                    if(!this.file_model) {
                        this.$store.dispatch('findSiteFile', {
                            custom : false,
                            file : this.file,
                            site : this.$route.params.site_id
                        }).then((file) => {
                            this.file_model = file;
                        });
                    }
                }
            },
            saveFile() {
                this.$store.dispatch('updateSiteFile', {
                    file: this.file,
                    site: this.site.id,
                    content: this.getContent(),
                    file_id: this.file_model.id,
                    servers: this.selected_servers,

                });
            },
            getContent() {
                return ace.edit($(this.$el).find('.editor')[0]).getValue();
            }
        },
        computed : {
            filePath() {
              if(_.isObject(this.file)) {
                  return this.file.file_path;
              }

              return this.file;
            },
            siteFiles() {
                return this.$store.state.siteFilesStore.site_files;
            }
        }
    }
</script>