<template>
    <section>
        <div class="jcf-form-wrap">
            <form class="floating-labels">
                <template v-if="hasLanguageItems">
                    <template v-for="(settings, language) in availableLanguageSettings">
                        <h1>{{ language }}'s settings</h1>
                        <template v-for="setting in settings">
                            <language-setting :setting="setting" :languageSettings="languageSettings"></language-setting>
                        </template>
                    </template>
                </template>
                <template v-else>
                    <h3>No language settings available.</h3>
                </template>
            </form>
        </div>
    </section>
</template>

<script>
    import LanguageSetting from '../components/LanguageSetting.vue'
    export default {
        components: {
          LanguageSetting
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
                    this.$store.dispatch('user_site_language_settings/get', this.siteId)
                    this.$store.dispatch('user_site_language_settings/getAvailable', this.siteId)
                }

                if(this.serverId) {
                    this.$store.dispatch('getServerLanguageSettings', this.serverId)
                    this.$store.dispatch('getServerAvailableLanguageSettings', this.serverId)
                }
            },
            isRunningCommandFor(id) {
                return this.isCommandRunning('App\\Models\\LanguageSetting', id)
            },
        },
        computed: {
            site() {
                return this.$store.state.user_sites.site
            },
            siteId() {
                return this.$route.params.site_id
            },
            serverId() {
                return this.$route.params.server_id
            },
            languageSettings() {
                if (this.siteId) {
                    return this.$store.state.user_site_language_settings.language_settings
                }

                if (this.serverId) {
                    return this.$store.state.serverLanguageSettingsStore.server_language_settings
                }
            },
            availableLanguageSettings() {
                if (this.siteId) {
                    return this.$store.state.user_site_language_settings.available_language_settings
                }

                if (this.serverId) {
                    return this.$store.state.serverLanguageSettingsStore.available_language_settings
                }
            },
            hasLanguageItems() {
                return !_.isEmpty(this.availableLanguageSettings)
            }
        }
    }
</script>