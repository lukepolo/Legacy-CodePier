<template>
    <div>
        <template v-if="site.repository">
            <li>
                <router-link :class="classes" :to="{ name : 'server_form_with_site' , params : { site : site.id , type : 'full_stack' } }">
                    <span class="icon-server"></span> Create Full Stack Server
                </router-link>
            </li>
            <!--<div class="btn btn-primary">Create Web Server</div>-->
            <!-- - not available during beta-->
            <!--<div class="btn btn-primary">Create Load Balance</div>-->
            <!-- - not available during beta-->
            <!--<div class="btn btn-primary">Create Database Server</div>-->
            <!-- - not available during beta-->
            <!--<div class="btn btn-primary">Create Queue Worker Serer</div>-->
            <!-- - not available during beta-->
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
        computed: {
            site() {
                return this.$store.state.sitesStore.site;
            }
        }
    }
</script>