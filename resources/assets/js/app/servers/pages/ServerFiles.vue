<template>
    <div v-if="server">
        <h3 class="flex--grow">
            Server Files
        </h3>
        <template v-if="files && server">
            <file :server="server" :forceShow="serverFiles.length === 1" :file="file" v-for="file in files" :key="file.id" :running="isRunningCommandFor(file)"></file>
        </template>
        <custom-files></custom-files>
    </div>
</template>

<script>
import File from "./../../setup/components/File";
import CustomFiles from "./../../setup/components/CustomFiles";
export default {
  components: {
    File,
    CustomFiles
  },
  created() {
    this.fetchData();
  },
  watch: {
    $route: "fetchData"
  },
  methods: {
    fetchData() {
      this.$store.dispatch(
        "user_server_files/get",
        this.$route.params.server_id
      );
    },
    isRunningCommandFor(file) {
      if (this.serverFiles) {
        let foundFile = _.find(this.serverFiles, { file: file.id });
        if (foundFile) {
          return this.isCommandRunning("App\\Models\\File", file.id);
        }
      }
      return false;
    }
  },
  computed: {
    files() {
      return this.serverFiles.filter(file => {
        return !file.custom && !file.framework_file;
      });
    },
    server() {
      return this.$store.state.user_servers.server;
    },
    serverFiles() {
      return this.$store.state.user_server_files.files;
    },
    runningCommands() {
      return this.$store.state.commands.running_commands;
    }
  }
};
</script>