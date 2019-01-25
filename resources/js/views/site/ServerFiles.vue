<template>
  <div class="list">
    <template v-for="file in files">
      <file class="list--item" :file="file" @update="updateFile"></file>
    </template>
  </div>
</template>

<script>
import File from "@views/common/File";
export default {
  components: {
    File,
  },
  watch: {
    $route: {
      immediate: true,
      handler() {
        this.$store.dispatch("user/sites/files/get", {
          site: this.siteId,
        });
      },
    },
  },
  methods: {
    updateFile(file) {
      this.$store.dispatch("user/sites/files/update", {
        parameters: {
          file: file.id,
          site: this.siteId,
        },
        data: {
          contents: file.contents,
        },
      });
    },
  },
  computed: {
    siteId() {
      return this.$route.params.site;
    },
    files() {
      return this.$store.state.user.sites.files.files.filter((file) => {
        return file.framework_file === 0 || file.custom === 0;
      });
    },
  },
};
</script>
