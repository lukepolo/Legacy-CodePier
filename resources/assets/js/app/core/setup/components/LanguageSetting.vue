<template>
    <section>
        <h3>
            {{ setting.name }}
            <br>
            <small>{{ setting.description }}</small>
        </h3>
        <template v-for="param in setting.params">
            <div class="jcf-input-group">
                <input type="text" :name="param" v-model="form.params['' + param +'']">
                <label>
                    <span class="float-label">{{ ucwords(param) }}</span>
                </label>
            </div>
        </template>

        <template v-if="isCommandCurrentlyRunning">
            {{ isCommandCurrentlyRunning.status }}
        </template>
        <template v-else>
            <div class="btn btn-primary" @click="runSetting">
                <template v-if="setting.params.length">
                    Update {{ setting.name }}
                </template>
                <template v-else>
                    Run {{ setting.name }}
                </template>
            </div>
        </template>
    </section>

</template>

<script>
    export default {
        props : ['setting', 'params', 'languageSettings'],
        data() {
            return {
                form : {
                    params : {}
                }
            }
        },
        created() {
            // Some weird reactivity issue, but this solves it by setting null first
            let languageSetting = null
            if(this.setting.params) {
                languageSetting = this.languageSetting
                _.each(this.setting.params, (param) => {
                    this.form.params[param] = languageSetting ? languageSetting['params'][param] : null
                })
            }
        },
        methods : {
            runSetting() {
                if(this.siteId) {
                    this.$store.dispatch('user_site_language_settings/run', {
                        site : this.siteId,
                        params : this.form.params,
                        setting : this.setting.name,
                        language : this.setting.type,
                    })
                }

                if(this.serverId) {
                    this.$store.dispatch('user_server_language_settings/run', {
                        server: this.serverId,
                        params: this.form.params,
                        setting: this.setting.name,
                        language: this.setting.type,
                    })
                }

            },
            ucwords(param) {
                return _.startCase(param);
            },

        },
        computed : {
            siteId() {
                return this.$route.params.site_id
            },
            serverId() {
                return this.$route.params.server_id
            },
            languageSetting() {
                return _.find(this.languageSettings, (languageSetting) => {
                    return languageSetting.languageSetting == this.setting.language && languageSetting.setting == this.setting.name;
                })
            },
            isCommandCurrentlyRunning() {
                if(this.languageSetting) {
                    return this.isCommandRunning('App\\Models\\LanguageSetting', this.languageSetting.id)
                }
            },
        }
    }
</script>