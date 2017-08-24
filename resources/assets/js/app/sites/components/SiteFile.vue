<template>
    <form @submit.prevent="saveFile()">
        {{ filePath }}
        <div class="jcf-form-wrap" v-if="site_servers">
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

        <div ref="editor" v-file-editor class="editor"></div>

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
                reload_server : null,
            }
        },
        watch: {
            '$route': 'fetchData',
            'fileModel' : function() {
                if(_.isObject(this.fileModel)) {
                    let editor = this.$refs.editor;
                    ace.edit(editor).setValue(this.fileModel.unencrypted_content);
                    ace.edit(editor).clearSelection(1);
                }
            }
        },
        methods: {
            saveFile() {
                this.$store.dispatch('user_site_files/update', {
                    site: this.site.id,
                    file_path: this.file,
                    content: this.getContent(),
                    file_id: this.fileModel.id,
                });
            },
            fetchData() {
                if(!_.isObject(this.file)) {
                    this.$store.dispatch('user_site_files/find', {
                        custom : false,
                        file : this.file,
                        site : this.$route.params.site_id
                    })
                }
            },
            reloadFile() {
                this.$store.dispatch('user_site_files/reload', {
                    file : this.fileModel.id,
                    server : this.reload_server,
                    site : this.$route.params.site_id,
                })
            },
            getContent() {
                return ace.edit(this.$refs.editor).getValue();
            },
        },
        computed : {
            fileModel() {
                return _.find(this.siteFiles, (file) => {
                    return this.file ===  file.file_path;
                });
            },
            site_servers() {

                let servers = this.$store.getters['user_site_servers/getServers'](this.$route.params.site_id)

                let server = _.first(servers);
                if(server) {
                    this.reload_server = server.id;
                }

                return servers
            },
            filePath() {
              if(_.isObject(this.file)) {
                  return this.file.file_path;
              }

              return this.file;
            },
            siteFiles() {
                return this.$store.state.user_site_files.files;
            }
        }
    }
</script>