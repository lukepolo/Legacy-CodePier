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
                        }">

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
                return this.$store.state.server_types.types
            }
        }
    }
</script>