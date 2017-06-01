<template>
    <section>

        <div class="jcf-form-wrap">

            <form @submit.prevent="createEnvironmentVariable" class="floating-labels">

                <div class="jcf-input-group">
                    <input type="text" name="variable" v-model="form.variable">

                    <label for="variable">
                        <span class="float-label">Variable</span>
                    </label>
                </div>

                <div class="jcf-input-group">
                    <input type="text" name="value" v-model="form.value">

                    <label for="value">
                        <span class="float-label">Value</span>
                    </label>
                </div>

                <div class="btn-footer">
                    <button class="btn btn-primary" type="submit">Create Environment Variable</button>
                </div>

            </form>

            <br>
            <table class="table" v-if="environmentVariables">
                <thead>
                <tr>
                    <th>Variable</th>
                    <th>Value</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="environmentVariable in environmentVariables">
                    <td>{{ environmentVariable.variable }}</td>
                    <td>{{ environmentVariable.value }}</td>
                    <td>
                        <template v-if="isRunningCommandFor(environmentVariable.id)">
                            {{ isRunningCommandFor(environmentVariable.id).status }}
                        </template>
                        <template v-else>
                            <a @click="deleteEnvironmentVariable(environmentVariable.id)" href="#" class="fa fa-remove">X</a>
                        </template>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>

        <input type="hidden" v-if="site">

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
                    this.$store.dispatch('user_site_environment_variables/get', this.siteId)
                }

                if(this.serverId) {
                    this.$store.dispatch('user_server_environment_variables/get', this.serverId)
                }
            },
            createEnvironmentVariable() {

                if(this.siteId) {
                    this.$store.dispatch('user_site_environment_variables/store', {
                        site: this.siteId,
                        value : this.form.value,
                        variable: this.form.variable,
                    }).then((environmentVariable) => {
                        if(environmentVariable) {
                            this.resetForm()
                        }
                    })
                }

                if(this.serverId) {
                    this.$store.dispatch('user_server_environment_variables/store', {
                        server: this.serverId,
                        value : this.form.value,
                        variable: this.form.variable,
                    }).then((environmentVariable) => {
                        if(environmentVariable) {
                            this.resetForm()
                        }
                    })
                }

            },
            deleteEnvironmentVariable(environmentVariableId) {

                if(this.siteId) {
                    this.$store.dispatch('user_site_environment_variables/destroy', {
                        site : this.siteId,
                        environment_variable : environmentVariableId
                    });
                }

                if(this.serverId) {
                    this.$store.dispatch('user_server_environment_variables/destroy', {
                        server : this.serverId,
                        environment_variable : environmentVariableId
                    });
                }

            },
            isRunningCommandFor(id) {
                return this.isCommandRunning('App\\Models\\EnvironmentVariable', id)
            },
            resetForm() {
                this.form.value = null
                this.form.variable = null
            }
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
            environmentVariables() {
                if (this.siteId) {
                    return this.$store.state.user_site_environment_variables.environment_variables
                }

                if (this.serverId) {
                    return this.$store.state.user_server_environment_variables.environment_variables
                }
            }
        }
    }
</script>