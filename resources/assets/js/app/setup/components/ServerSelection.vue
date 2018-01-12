<template>
    <div class="btn--trigger-panel">
        <tooltip message="Servers to Run On" v-if="displayServerSelection"><span class="icon-server" @click="showModal()"></span></tooltip>
        <modal v-if="showingModal">
            <template v-if="site">
                <h3 class="section-header">Select Servers</h3>
                <div class="section-content section-content-padding">
                    <p>By default we install on all servers. To select specific servers, choose from the list below:</p>

                    <h4 class="text-primary">{{ title }}</h4>

                    <br>

                    <ul class="wizard">
                        <li class="wizard-item" :class="{ 'router-link-active' : showServerTypes }" @click="showServerTypes = true">
                            <a>Server Types</a>
                        </li>
                        <li class="wizard-item" :class="{ 'router-link-active' : !showServerTypes }" @click="showServerTypes = false">
                            <a>Specific Servers</a>
                        </li>
                    </ul>

                    <template v-for="(serverType, serverTypeName) in availableServerTypes" v-if="showServerTypes">
                        <div class="flyform--group-checkbox">
                            <label>
                                <input type="checkbox" v-model="form.server_types" :value="serverType">
                                <span class="icon"></span>
                                {{ serverTypeName }}
                            </label>
                        </div>
                    </template>

                    <template v-for="server in siteServers" v-if="!showServerTypes">
                        <div class="flyform--group-checkbox">
                            <label>
                                <input type="checkbox" v-model="form.server_ids" :value="server.id"
                                       :disabled="serverTypeSelected(server.type)">
                                <span class="icon"></span>
                                {{ server.name }} <small>({{ server.type | startCase }}) <br> <span v-if="server.ip">({{ server.ip }})</span></small>
                            </label>
                        </div>
                    </template>

                    <div class="flyform--footer">
                        <div class="flyform--footer-btns">
                            <span class="btn btn-small" @click="close()">Close</span>
                        </div>
                    </div>
                </div>
            </template>
        </modal>
    </div>
</template>

<script>
export default {
  props: {
    title: {
      default: ""
    },
    server_ids: {
      default: () => []
    },
    server_types: {
      default: () => []
    },
    availableServerTypes: {
      default: () => window.Laravel.serverTypes
    }
  },
  data() {
    return {
      form: {
        server_ids: this.server_ids ? this.server_ids : [],
        server_types: this.server_types ? this.server_types : []
      },
      showingModal: false,
      showServerTypes: true
    };
  },
  created() {
    app.$on("close-server-selections", () => {
      this.close();
    });
  },
  watch: {
    "form.server_ids": function() {
      this.$emit("update:server_ids", this.form.server_ids);
    },
    "form.server_types": function() {
      this.$emit("update:server_types", this.form.server_types);
    }
  },
  methods: {
    close() {
      Vue.set(this, "showingModal", false);
    },
    serverTypeSelected(serverType) {
      return _.find(this.form.server_types, selectedServerType => {
        return selectedServerType === serverType;
      });
    },
    showModal() {
      app.$emit("close-server-selections");
      Vue.nextTick(() => {
        Vue.set(this, "showingModal", true);
      });
    }
  },
  computed: {
    site() {
      return this.$store.state.user_sites.site;
    },
    siteServers() {
      return _.filter(
        this.$store.getters["user_site_servers/getServers"](
          this.$route.params.site_id
        ),
        server => {
          return _.find(this.availableServerTypes, serverType => {
            return server.type === serverType;
          });
        }
      );
    },
    displayServerSelection() {
      if (this.$route.params.site_id) {
        if (this.siteServers && this.siteServers.length > 1) {
          return true;
        }
        return false;
      }
    }
  }
};
</script>
