<template>
    <div v-if="serverId">
        <div class="tab-container tab-left">
            <ul class="nav nav-tabs">

                <router-link :to="{ name : 'server_environment_variables', params : { server_id : serverId } }" tag="li" exact>
                    <a>
                        Environment Variables
                        <div class="small">Add environment variables for you applications</div>
                    </a>
                </router-link>

                <router-link :to="{ name : 'server_cron_jobs', params : { server_id : serverId } }" tag="li" exact>
                    <a>
                        Cron Jobs
                        <div class="small">Configure cron jobs</div>
                    </a>
                </router-link>


                <router-link :to="{ name : 'server_workers', params : { server_id : serverId } }" tag="li">
                    <a>
                        Workers
                        <div class="small">Configure workers / daemons services</div>
                    </a>
                </router-link>

                <router-link :to="{ name : 'server_files', params : { server_id : serverId } }" tag="li">
                    <a>
                        Server Files
                        <div class="small">Customize your server files</div>
                    </a>
                </router-link>

                <router-link :to="{ name : 'server_features', params : { server_id : serverId } }" tag="li">
                    <a>
                        Server Features
                        <div class="small">Advanced server setup, for highly customized servers</div>
                    </a>
                </router-link>

            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active">
                    <slot></slot>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
    export default{
        computed : {
            serverId() {
                return this.$route.params.server_id
            }
        },
        watch: {
            '$route': function() {
                $('#middle .section-content').scrollTop(0)
            }
        }
    }
</script>