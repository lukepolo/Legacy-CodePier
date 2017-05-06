<template>
    <section>
        Language Settings
         {{ availableLanguageSettings }}
    </section>
</template>

<script>
    export default {
        data() {
            return {
                form : {
                    value: '',
                    variable: null,
                }
            }
        },
        created() {
            this.fetchData()
        },
        watch: {
            '$route': 'fetchData'
        },
        methods: {
            fetchData() {
                if(this.siteId) {
                    this.$store.dispatch('getSiteLanguageSettings', this.siteId)
                    this.$store.dispatch('getAvailableLanguageSettings', this.siteId)
                }

                if(this.serverId) {
//                    this.$store.dispatch('getServerEnvironmentVariables', this.serverId)
                }
            },
            isRunningCommandFor(id) {
                return this.isCommandRunning('App\\Models\\LanguageSetting', id)
            },
        },
        computed: {
            site() {
                return this.$store.state.sitesStore.site
            },
            siteId() {
                return this.$route.params.site_id
            },
            serverId() {
                return this.$route.params.server_id
            },
            languageSettings() {
                if (this.siteId) {
                    return this.$store.state.siteEnvironmentVariablesStore.site_environment_variables
                }

                if (this.serverId) {
                    return this.$store.state.serverEnvironmentVariablesStore.server_environment_variables
                }
            },
            availableLanguageSettings() {
                if (this.siteId) {
                    return this.$store.state.siteLanguageSettingsStore.available_language_settings
                }

                if (this.serverId) {
                }
            }
        }
    }
</script>