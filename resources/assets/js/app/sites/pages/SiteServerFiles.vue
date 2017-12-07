<template>
    <section>
        <template v-if="site && files">
            <site-file :site="site" :file="file" v-for="file in files" :key="file.id" :running="isRunningCommandFor(file)"></site-file>
        </template>
    </section>
</template>

<script>

  import {
    SiteFile,
  } from '../components'

  export default {
    components: {
      SiteFile,
    },
    created () {
      this.fetchData()
    },
    watch: {
      '$route': 'fetchData'
    },
    methods: {
      fetchData () {
        this.$store.dispatch('user_site_files/get', this.$route.params.site_id)
      },
      isRunningCommandFor (file) {
        if (this.siteFiles) {
          let foundFile = _.find(this.siteFiles, {file_path: file})
          if (foundFile) {
            return this.isCommandRunning('App\\Models\\File', foundFile.id)
          }
        }
        return false
      }
    },
    computed: {
      runningCommands () {
        return this.$store.state.commands.running_commands
      },
      site () {
        return this.$store.state.user_sites.site
      },
      files () {
        return this.siteFiles.filter((file) => {
            return (!file.custom && !file.framework_file);
        });
      },
      siteFiles () {
        return this.$store.state.user_site_files.files
      }
    },
  }
</script>