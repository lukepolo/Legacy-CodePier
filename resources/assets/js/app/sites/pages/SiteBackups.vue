<template>
    <div>
        <div class="flex flex--center">
            <h3 class="flex--grow">
                Database Backups
            </h3>

            // TODO - need a toggle of some sort?
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
          this.$store.dispatch(
            "user_site_schemaBackups/get",
            this.$route.params.site_id
          );
        },
        downloadBackup(backup) {
          this.$store.dispatch("user_site_schemaBackups/download", {
            backup: backup.id,
            site: this.$route.params.site_id
          });
        }
      },
      computed : {
        backups() {
          return this.$store.state.user_site_schemaBackups.backups;
        }
      }
    }
</script>