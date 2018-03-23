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

        <table class="table" v-if="backups.length">
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
            <tr v-for="backup in backups">
                <td>{{ backup.name }}</td>
                <td>{{ backup.type }}</td>
                <td>{{ backup.created_at }}</td>
                <td class="table--action">
                    <button class="btn btn-default btn-small">Restore</button>
                    <button class="btn btn-default btn-small" @click="downloadBackup(backup)">Download</button>
                </td>
            </tr>
            </tbody>
        </table>

        <div v-for="server in siteServers">

            {{ server.name }} ({{ server.ip }})

            <div class="toggleSwitch">
                <div class="toggleSwitch--button toggleSwitch--button-switch" :class="{ right : server.backups }"
                @click="toggleBackups(server)"></div>
            </div>

        </div>
    </div>
</template>

<script>
    export default {
      watch: {
        '$route' : {
          handler : 'fetchData',
          immediate : true
        },
      },
      methods : {
        fetchData() {
          // this.$store.dispatch(
          //   "user_site_schemaBackups/get",
          //   this.$route.params.site_id
          // );
        },
        toggleBackups(server) {
            if(server.backups) {
              return this.disableBackups(server);
            }
            this.enableBackups(server)
        },
        enableBackups(server) {
          this.$store.dispatch("user_server_schemaBackups/enable", server.id)
        },
        disableBackups(server) {
          this.$store.dispatch("user_server_schemaBackups/disable", server.id)
        },
        downloadBackup(backup) {
          this.$store.dispatch("user_site_schemaBackups/download", {
            backup: backup.id,
            site: this.$route.params.site_id
          });
        },
      },
      computed : {
        backups() {
          return [];
          // return this.$store.state.user_site_schemaBackups.backups;
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