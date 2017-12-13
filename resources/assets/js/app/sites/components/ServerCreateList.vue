<template>
    <div class="server-type-list">
        <p>Select the type of server you need.</p>

        <ul>
            <li v-for="(serverType, serverTypeText) in serverTypes">
                <template v-if="site.repository">

                    <router-link
                        :to="{
                            name : 'server_form_with_site' ,
                            params : {
                                site_id : site.id ,
                                type : serverType
                            }
                        }"
                        :disabled="!serverTypesEnabled"
                    >
                        {{ serverTypeText }} Server
                    </router-link>

                </template>
            </li>
        </ul>
    </div>
</template>

<script>
    export default {
        props : {
            classes : {
                default : ''
            }
        },
        created() {
            this.$store.dispatch('server_types/get')
        },
        computed: {
            site() {
                return this.$store.state.user_sites.site;
            },
            serverTypes() {
                return _.pickBy(this.$store.state.server_types.types, (type) => {
                    if(this.hasLoadBalancer && type === 'load_balancer') {
                        return false;
                    }

                    return true;
                })
            },
            siteServers() {
                return this.$store.getters['user_site_servers/getServers'](this.$route.params.site_id)
            },
            hasLoadBalancer() {
                return _.filter(this.siteServers, function(server) {
                    return server.type === 'load_balancer'
                }).length > 0
            }
        }
    }
</script>