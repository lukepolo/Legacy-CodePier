<template>
    <div class="list">
        <template v-if="siteFiles">
            <file :forceShow="siteFiles.length === 1" :file="file" v-for="file in files" :key="file.id" :running="isRunningCommandFor(file)"></file>
        </template>
    </div>
</template>

<script>
import File from "./../../setup/components/File";
export default {
  components: {
    File
  },
  methods: {
    isRunningCommandFor(file) {
      if (this.files) {
        let foundFile = _.find(this.files, { id: file.id });
        if (foundFile) {
          return this.isCommandRunning("App\\Models\\File", file.id);
        }
      }

      return false;
    }
  },
  computed: {
    site() {
      return this.$store.state.user_sites.site;
    },
    files() {
      return this.siteFiles.filter(file => {
        return file.framework_file;
      });
    },
    siteFiles() {
      return this.$store.state.user_site_files.files;
    }
  }
};
</script>