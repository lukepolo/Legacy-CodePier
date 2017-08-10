<template>
    <section>
        <template v-if="site && displayServerSelection">
            <br>
            <br>
            <h3>By default we install these all on all servers, you show pick the servers that you want these to run on</h3>
            <template v-for="server in servers">
                <div class="flyform--group-checkbox">
                    <label>
                        <input type="checkbox" v-model="form.servers" :value="server.id" :disabled="serverTypeSelected(server.type)">
                        <span class="icon"></span>
                        {{ server.name }} - {{ server.type }} -({{ server.ip }})
                    </label>
                </div>
            </template>

            <template v-for="(serverType, serverTypeName) in availableServerTypes">
                <div class="flyform--group-checkbox">
                    <label>
                        <input type="checkbox" v-model="form.server_types" :value="serverType">
                        <span class="icon"></span>
                        {{ serverTypeName }}
                    </label>
                </div>
            </template>
        </template>
    </section>
</template>

<script>
    export default {
        props : {
            availableServerTypes : {
                default : () => window.Laravel.serverTypes
            }
        },
        data() {
            return {
                form : {
                    servers : [],
                    server_types : []
                }
            }
        },
        watch: {
            'form.servers': function() {
                this.$emit('update:servers', this.form.servers)
            },
            'form.server_types': function() {
                this.$emit('update:server_types', this.form.server_types)
            }
        },
        methods : {
            serverTypeSelected(serverType) {
                return _.find(this.form.server_types, (selectedServerType) => {
                    return selectedServerType === serverType
                })
            }
        },
        computed : {
            site() {
                return this.$store.state.user_sites.site
            },
            servers() {
                return _.filter(this.$store.getters['user_site_servers/getServers'](this.$route.params.site_id), (server) => {
                    return _.find(this.availableServerTypes, (serverType) => {
                        return server.type === serverType
                    })
                })
            },
            displayServerSelection() {
                if(this.$route.params.site_id) {
                    if(this.servers && this.servers.length > 1) {
                        return true
                    }
                    return false
                }
            },
        }
    }
</script>