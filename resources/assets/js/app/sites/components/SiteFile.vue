<template>
    <div>
        <div class="list--item list--item-icons list--item-click" @click="showFile = !showFile"  :class="{ 'selected' : this.showFile }" >
            {{ filePath }}
            <span class="icon-arrow-right"></span>
        </div>
        <collapse>
            <div class="editor" v-show="showFile">
                <form @submit.prevent="saveFile()">

                    <div ref="editor" v-file-editor class="editor"></div>

                    <div class="editor--actions">

                        <div class="flyform--footer-btns" v-if="!isReloading">
                            <tooltip message="Fetch File from Remote Server" class="btn--tooltip">
                                <a @click="isReloading = true" class="btn"><span class="icon-refresh2"></span></a>
                            </tooltip>


                            <button class="btn btn-primary" :class="{ 'btn-disabled' : running }" type="submit">
                                <template v-if="!running">
                                    Update File
                                </template>
                                <template v-else>
                                    File {{ running.status }}
                                </template>
                            </button>

                            <button @click.prevent="deleteFile" v-if="deletable">Delete</button>
                        </div>

                        <div class="flex flex--baseline" v-if="isReloading">
                            <div class="flyform--group flex--grow" v-if="site_servers">
                                <label class="flyform--group-iconlabel">Select a Server</label>
                                <tooltip message="We can fetch a file and replace all content inside by reloading the file"
                                         class="long">
                                    <span class="fa fa-info-circle"></span>
                                </tooltip>

                                <div class="flyform--group-select">
                                    <select name="server" v-model="reload_server">
                                        <option v-for="server in site_servers" :value="server.id">{{ server.name }} - {{ server.ip }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="flex--spacing">
                                <span class="btn btn-small" @click="isReloading = false"><span class="icon-x"></span></span>
                                <tooltip message="Reload from Selected Server" class="btn--tooltip-small">
                                    <button @click="reloadFile" class="btn btn-small btn-primary"><span class="icon-refresh2"></span></button>
                                </tooltip>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </collapse>
    </div>
</template>

<script>
  export default {
    props: ['site', 'file', 'running', 'forceShow', 'deletable'],
    created () {
      this.fetchData()
    },
    data () {
      return {
        showFile : this.forceShow,
        reload_server: null,
        isReloading : false,
      }
    },
    watch: {
      '$route': 'fetchData',
    },
    methods: {
      renderContent() {
        if (_.isObject(this.file)) {
          let editor = this.$refs.editor
          ace.edit(editor).setValue(this.file.unencrypted_content ? this.file.unencrypted_content : '')
          ace.edit(editor).clearSelection(1)
        }
      },
      deleteFile() {
        console.info(this.fileModel)
        this.$store.dispatch('user_site_files/destroy', {
          site : this.site.id,
          file : this.fileModel.id
        })
      },
      saveFile () {
        this.$store.dispatch('user_site_files/update', {
          site: this.site.id,
          content: this.getContent(),
          file_id: this.fileModel.id,
        })
      },
      fetchData () {
        if (!_.isObject(this.file)) {
          this.$store.dispatch('user_site_files/find', {
            custom: false,
            file: this.file,
            site: this.$route.params.site_id
          }).then((file) => {
            Vue.set(this, 'file', file)
            this.renderContent();
          })
        } else {
          console.info('here')
          this.renderContent()
        }
      },
      reloadFile () {
        this.$store.dispatch('user_site_files/reload', {
          file: this.fileModel.id,
          server: this.reload_server,
          site: this.$route.params.site_id,
        })
        .then(() => {
            this.isReloading = false;
        })
      },
      getContent () {
        return ace.edit(this.$refs.editor).getValue()
      },
    },
    computed: {
      site_servers () {

        let servers = this.$store.getters['user_site_servers/getServers'](this.$route.params.site_id)

        let server = _.first(servers)
        if (server) {
          this.reload_server = server.id
        }

        return servers
      },
      filePath () {
        if (_.isObject(this.file)) {
          return this.file.file_path
        }

        return this.file
      },
      siteFiles () {
        return this.$store.state.user_site_files.files
      },
//      shouldShowFile() {
//        return this.forceShow ? true : this.showFile;
//      }
    }
  }
</script>