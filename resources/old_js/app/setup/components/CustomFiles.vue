<template>
    <div>
        <div class="list">
            <file :deletable="true" :file="file" v-for="file in files" :running="isRunningCommandFor(file)" :key="file.id"></file>
        </div>

        <form @submit.prevent="addCustomFile" class="flex flex--baseline">
            <div class="flyform--group flex--grow">
                <input type="text" name="file" v-model="form.file" placeholder=" ">
                <label for="file">File Path</label>
            </div>

            <div class="flex--spacing">
                <button class="btn btn-primary btn-small" type="submit">Add Custom File</button>
            </div>
        </form>
    </div>
</template>

<script>
import File from "./File";

export default {
  data() {
    return {
      form: this.createForm({
        file: "",
      }),
    };
  },
  components: {
    File,
  },
  methods: {
    isRunningCommandFor(file) {
      if (this.siteId && this.modelFiles) {
        let foundFile = _.find(this.modelFiles, { id: file.id });
        if (foundFile) {
          return this.isCommandRunning("App\\Models\\File", foundFile.id);
        }
      }
      return false;
    },
    addCustomFile() {
      if (this.siteId) {
        this.$store
          .dispatch("user_site_files/find", {
            custom: true,
            site: this.siteId,
            file: this.form.file,
          })
          .then(() => {
            this.form.file = "";
          });
      }

      if (this.serverId) {
        this.$store
          .dispatch("user_server_files/find", {
            custom: true,
            file: this.form.file,
            server: this.serverId,
          })
          .then(() => {
            this.form.file = "";
          });
      }
    },
  },
  computed: {
    runningCommands() {
      return this.$store.state.commands.running_commands;
    },
    siteId() {
      return this.$route.params.site_id;
    },
    serverId() {
      return this.$route.params.server_id;
    },
    site() {
      return this.$store.state.user_sites.site;
    },
    server() {
      return this.$store.state.user_servers.server;
    },
    files() {
      return _.filter(this.modelFiles.files, function(file) {
        return file.custom;
      });
    },
    modelFiles() {
      if (this.siteId) {
        return this.$store.state.user_site_files;
      }

      if (this.serverId) {
        return this.$store.state.user_server_files;
      }
    },
  },
};
</script>
