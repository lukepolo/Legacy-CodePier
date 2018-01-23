<template>
    <div>
        <div class="list--item list--item-icons list--item-click" @click="showFile = !showFile"  :class="{ 'selected' : this.showFile }" >
            {{ file.file_path }}
            <span class="icon-arrow-right"></span>
        </div>
        <collapse>
            <div class="editor" v-show="showFile">
                <form @submit.prevent="saveFile()">

                    <div ref="editor" v-file-editor class="editor"></div>

                    <div class="editor--actions">

                        <div class="flyform--footer-btns" v-if="!isReloading">
                            <tooltip message="Fetch File from Remote Server" class="btn--tooltip" v-if="siteServers">
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

                        <div class="flex flex--baseline" v-if="isReloading && siteId">
                            <div class="flyform--group flex--grow" v-if="siteServers">
                                <label class="flyform--group-iconlabel">Select a Server</label>
                                <tooltip message="We can fetch a file and replace all content inside by reloading the file"
                                         class="long">
                                    <span class="fa fa-info-circle"></span>
                                </tooltip>

                                <div class="flyform--group-select">
                                    <select name="server" v-model="reload_server">
                                        <option v-for="server in siteServers" :value="server.id">{{ server.name }} - {{ server.ip }}</option>
                                    </select>
                                </div>
                            </div>

                            <div class="flex--spacing">
                                <span class="btn btn-small" @click="isReloading = false"><span class="icon-x"></span></span>
                                <tooltip message="Reload from Selected Server" class="btn--tooltip-small">
                                    <div @click="reloadFile" class="btn btn-small btn-primary"><span class="icon-refresh2"></span></div>
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
  props: ["file", "running", "forceShow", "deletable"],
  data() {
    return {
      showFile: this.forceShow,
      reload_server: null,
      isReloading: false
    };
  },
  mounted() {
    this.renderContent();
  },
  methods: {
    renderContent() {
      let editor = this.$refs.editor;
      ace
        .edit(editor)
        .setValue(
          this.file.unencrypted_content ? this.file.unencrypted_content : ""
        );
      ace.edit(editor).clearSelection(1);
    },
    deleteFile() {
      if (this.siteId) {
        this.$store.dispatch("user_site_files/destroy", {
          site: this.siteId,
          file: this.file.id
        });
      }

      if (this.serverId) {
        this.$store.dispatch("user_server_files/destroy", {
          server: this.serverId,
          file: this.file.id
        });
      }
    },
    saveFile() {
      if (this.siteId) {
        this.$store.dispatch("user_site_files/update", {
          site: this.siteId,
          content: this.getContent(),
          file_id: this.file.id
        });
      }

      if (this.serverId) {
        this.$store.dispatch("user_server_files/update", {
          server: this.serverId,
          content: this.getContent(),
          file_id: this.file.id
        });
      }
    },
    reloadFile() {
      this.$store
        .dispatch("user_site_files/reload", {
          file: this.file.id,
          server: this.reload_server,
          site: this.siteId
        })
        .then(() => {
          this.isReloading = false;
          this.renderContent();
        });
    },
    getContent() {
      return ace.edit(this.$refs.editor).getValue();
    }
  },
  computed: {
    siteId() {
      return this.$route.params.site_id;
    },
    serverId() {
      return this.$route.params.server_id;
    },
    siteServers() {
      let servers = this.$store.getters["user_site_servers/getServers"](
        this.siteId
      );

      let server = _.first(servers);
      if (server) {
        this.reload_server = server.id;
      }

      return servers;
    }
  }
};
</script>
