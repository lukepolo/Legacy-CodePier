<template>
    <div>
        <div class="flex flex--center">
            <h3 class="flex--grow">
                Database Backups
            </h3>

            <tooltip message="Add Firewall Rule">
                <span class="btn btn-small btn-primary">
                    <span class="icon-plus"></span>
                </span>
            </tooltip>
        </div>

        <div class="section-content">
            <div class="container">
                <ul class="wizard">
                    <template v-for="server in backups.servers">
                        <li class="wizard-item" v-bind:class="{ 'router-link-active': server.name }">
                            <a @click="tab=server.name">{{ server.name }}</a>
                        </li>
                    </template>
                </ul>
            </div>
            <div v-for="server in backups.servers">
                <div v-if="tab=server.name">
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
          tab : null
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
          return _.find(this.siteServers, { id : server.id }).backups_enabled
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