<template>

    <div class="server-type-list">

        <template v-if="site.repository">

            <li v-for="(serverType, serverTypeText) in serverTypes">

                <router-link
                    :class="classes"
                    :to="{ name : 'server_form_with_site' , params : {
                        site_id : site.id ,
                         type : serverType
                    }
                }">
                    <span class="icon-server"></span> Create {{ serverTypeText }}
                </router-link>

            </li>

        </template>

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