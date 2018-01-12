<template>
    <section>
        <div class="flex flex--center">
            <h3 class="flex--grow">
                Server Files
            </h3>
        </div>
        <template v-if="site && files">
            <file :site="site" :file="file" v-for="file in files" :key="file.id" :running="isRunningCommandFor(file)"></file>
        </template>
    </section>
</template>

<script>
import File from "./../../setup/components/File";

export default {
  components: {
    File
  },
  created() {
    this.fetchData();
  },
  watch: {
    $route: "fetchData"
  },
  methods: {
    fetchData() {
      this.$store.dispatch("user_site_files/get", this.$route.params.site_id);
    },
    isRunningCommandFor(file) {
      if (this.siteFiles) {
        let foundFile = _.find(this.siteFiles, { file_path: file });
        if (foundFile) {
          return this.isCommandRunning("App\\Models\\File", foundFile.id);
        }
      }
      return false;
    }
  },
  computed: {
    runningCommands() {
      return this.$store.state.commands.running_commands;
    },
    site() {
      return this.$store.state.user_sites.site;
    },
    files() {
      return this.siteFiles.filter(file => {
        return !file.custom && !file.framework_file;
      });
    },
    siteFiles() {
      return this.$store.state.user_site_files.files;
    }
  }
};
</script>
