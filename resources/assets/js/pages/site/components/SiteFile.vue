<template>
    <form @submit.prevent="saveFile()">
        {{ filePath }}
        <div class="jcf-form-wrap">
            <form>
                <div class="jcf-input-group">
                    <tooltip message="We can fetch a file and replace all content inside by reloading the file" class="long">
                        <span class="fa fa-info-circle"></span>
                    </tooltip>
                    <div class="input-question">Select a server</div>
                    <div class="select-wrap">
                        <select name="server" v-model="reload_server">
                            <option v-for="server in site_servers" :value="server.id">{{ server.name }} - {{ server.ip }}</option>
                        </select>
                    </div>
                </div>
                <a @click="reloadFile" class="btn btn-xs">Reload File From Selected Server</a>
            </form>
        </div>

        <div v-file-editor class="editor"></div>

        <div class="btn-footer">
            <button class="btn btn-primary" :class="{ 'btn-disabled' : running }" type="submit">
                <template v-if="!running">
                    Update File
                </template>
                <template v-else>
                    File {{ running.status }}
                </template>
            </button>
        </div>
    </form>
</template>

<script>
    export default {
        props: ['site', 'file', 'running'],
        created() {
            this.fetchData();
        },
        data() {
            return {
                file_model: null,
                reload_server : null,
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
                this.$store.dispatch('updateSiteFile', {
                    file: this.file,
                    site: this.site.id,
                    content: this.getContent(),
                    file_id: this.file_model.id,
                });
            },
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
            reloadFile() {
                this.$store.dispatch('reloadSiteFile', {
                    file : this.file_model.id,
                    server : this.reload_server,
                    site : this.$route.params.site_id,
                })
            },
            getContent() {
                return ace.edit($(this.$el).find('.editor')[0]).getValue();
            },
        },
        computed : {
            site_servers() {
                let siteServers = this.$store.state.sitesStore.site_servers[this.$route.params.site_id]
                if(siteServers) {
                    let server = _.first(siteServers);
                    if(server) {
                        this.reload_server = server.id;
                    }
                }

                return site_servers;
            },
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