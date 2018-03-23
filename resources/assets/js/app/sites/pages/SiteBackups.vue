<template>
    <div>
        <div class="flex flex--center">
            <h3 class="flex--grow">
                Database Backups
            </h3>
        </div>

        <div class="section-content">
            <ul class="wizard">
                <template v-for="(server, serverIndex) in siteServers">
                    <li class="wizard-item" v-bind:class="{ 'router-link-active': tab === serverIndex }">
                        <a @click="tab=serverIndex">{{ server.name }}</a>
                    </li>
                </template>
            </ul>
            <div v-for="(server, serverIndex) in backups.servers">
                <div v-if="tab === serverIndex">
                    <div class="toggleSwitch">
                        Enable Backups &nbsp;
                        <div
                            class="toggleSwitch--button toggleSwitch--button-switch"
                            :class="{ right : isBackupsEnabled(server) }"
                            @click="toggleBackups(server)"
                        ></div>
                    </div>
                    <table class="table" v-if="server.backups.length">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Type</th>
                                <th>Server</th>
                                <th>Date</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="backup in server.backups">
                                <td>{{ backup.name }}</td>
                                <td>{{ backup.type }}</td>
                                <td>{{ backup.created_at }}</td>
                                <td class="table--action">
                                    <!--<button class="btn btn-default btn-small">Restore</button>-->
                                    <button class="btn btn-default btn-small" @click="downloadBackup(server, backup)">Download</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
      data() {
        return {
          tab : 0
        }
      },
      watch: {
        '$route' : {
          handler : 'fetchData',
          immediate : true,
        },

      },
      methods : {
        fetchData() {
          this.$store.dispatch(
            "user_site_schema_backups/get",
            this.$route.params.site_id
          );
        },
        toggleBackups(server) {
            if(this.isBackupsEnabled(server)) {
              return this.disableBackups(server);
            }
            this.enableBackups(server)
        },
        enableBackups(server) {
          this.$store.dispatch("user_server_schema_backups/enable", server.id)
        },
        disableBackups(server) {
          this.$store.dispatch("user_server_schema_backups/disable", server.id)
        },
        downloadBackup(server, backup) {
          this.$store.dispatch("user_server_schema_backups/download", {
            backup: backup.id,
            server: server.id
          });
        },
        isBackupsEnabled(server) {
            let foundServer = _.find(this.siteServers, { id : server.id });
            if(foundServer) {
                return foundServer.backups_enabled
            }
          return false;
        }
      },
      computed : {
        backups() {
          return this.$store.state.user_site_schema_backups.backups;
        },
        siteServers() {
          return _.filter(
            this.$store.getters["user_site_servers/getServers"](
              this.$route.params.site_id
            ),
            server => {
              return server.type === 'full_stack' || server.type === 'database'
            }
          );
        },
      }
    }
</script>