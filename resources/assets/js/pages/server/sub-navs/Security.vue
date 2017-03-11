<template>
    <div v-if="serverId">
        <div class="tab-container tab-left">
            <ul class="nav nav-tabs">

                <router-link :to="{ name : 'server_ssh_keys', params : { server_id : serverId } }" tag="li" class="wizard-item" exact>
                    <a>
                        SSH Keys
                        <div class="small">Add ssh keys that are required to access the server</div>
                    </a>
                </router-link>

                <router-link :to="{ name : 'server_firewall_rules', params : { server_id : serverId } }" tag="li" class="wizard-item">
                    <a>
                        Firewall Rules
                        <div class="small">Setup your firewall rules</div>
                    </a>
                </router-link>

                <router-link :to="{ name : 'server_firewall_rules', params : { server_id : serverId } }" tag="li">
                    <a>
                        SSL Certificates -- TODO - we don't have this setup?!
                        <div class="small">Configure SSL certificates</div>
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
        }
    }
</script>