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

        <div class="btn btn-primary" @click="runSetting">
            <template v-if="setting.params.length">
                Update Setting
            </template>
            <template v-else>
                Run
            </template>
        </div>
    </section>

</template>

<script>
    export default {
        props : ['setting', 'params'],
        data() {
            return {
                form : {
                    params : {}
                }
            }
        },
        created() {
            if(this.setting.params) {
                _.each(this.setting.params, (param) => {
                    this.form.params[param] = null
                })
            }
        },
        methods : {
            runSetting() {
                if(this.siteId) {
                    this.$store.dispatch('runSiteLanguageSetting', {
                        site : this.siteId,
                        params : this.form.params,
                        setting : this.setting.name,
                        language : this.setting.type,
                    })
                }

                if(this.serverId) {
                    alert('server id')
                }

            },
            ucwords(param) {
                return _.startCase(param);
            }
        },
        computed : {
            siteId() {
                return this.$route.params.site_id
            },
            serverId() {
                return this.$route.params.server_id
            }
        }
    }
</script>