<template>
    <h3 class="section-header primary" v-if="site">
        <back></back>

        <a :href="'http://'+site.domain" target="_blank">
            {{ site.name }}
        </a>

        <div class="section-header--btn-right">

            <template v-if="site.user_repository_provider_id">
                <template v-if="site && site.repository">

                    <template v-if="!site.automatic_deployment_id">

                    <span class="dropdown" @click="createDeployHook">
                        <tooltip message="Enable Auto Deploy" placement="bottom-left" class="btn btn-default">
                            <span class="icon-cloud-auto-deploy"></span>
                        </tooltip>
                    </span>

                    </template>

                    <template v-else>

                    <span class="dropdown" @click="removeDeployHook">
                        <tooltip message="Remove Auto Deploy" placement="bottom-left" class="btn btn-default">
                            <span class="icon-cloud-auto-deploy active"></span>
                        </tooltip>
                    </span>

                    </template>

                </template>
            </template>


            <drop-down tag="span">

                <tooltip slot="header" message="Site SSH Key" placement="bottom-left" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                    <span class="icon-web"></span>
                </tooltip>

                <div slot="content" class="dropdown-menu dropdown-content nowrap">

                    <div class="jcf-form-wrap">

                        <div class="jcf-input-group">

                            <div class="input-question">
                                <confirm-dropdown dispatch="user_site_ssh_keys/refreshPublicKey" :params="site.id">Public SSH Key &nbsp;<a href="#"><span class="fa fa-refresh"></span></a></confirm-dropdown>
                            </div>

                            <textarea rows="10" readonly>{{ site.public_ssh_key }}</textarea>

                            <div class="text-right">
                                <clipboard :data="site.public_ssh_key"></clipboard>
                            </div>

                        </div>

                    </div>

                </div>

            </drop-down>

            <drop-down tag="span">

                <tooltip slot="header" message="Deploy Hook URL" placement="bottom-left" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                    <span class="icon-webhooks"></span>
                </tooltip>

                <div slot="content" class="dropdown-menu nowrap">

                    <div class="jcf-form-wrap">

                        <div class="jcf-input-group">

                            <div class="input-question">
                                <confirm-dropdown dispatch="user_site_deployments/refreshDeployKey" :params="site.id">Deploy Hook URL &nbsp;<a href="#"><span class="fa fa-refresh"></span></a></confirm-dropdown>
                            </div>

                            <textarea  rows="3" readonly :value="deployHook"></textarea>

                            <div class="text-right">
                                <clipboard :data="deployHook"></clipboard>
                            </div>

                        </div>

                    </div>

                </div>

            </drop-down>

            <drop-down tag="span" v-if="siteServers">

                <button slot="header" class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                    <span class="icon-server"></span>
                </button>

                <ul slot="content" class="dropdown-menu nowrap dropdown-list">
                    <li>
                        <confirm-dropdown dispatch="user_site_services/restartWebServices" :params="site.id"><a href="#"><span class="icon-web"></span> Restart Web Services</a></confirm-dropdown>
                    </li>
                    <li>
                        <confirm-dropdown dispatch="user_site_services/restartServers" :params="site.id"><a href="#"><span class="icon-server"></span> Restart Servers</a></confirm-dropdown>
                    </li>
                    <li>
                        <confirm-dropdown dispatch="user_site_services/restartDatabases" :params="site.id"><a href="#"><span class="icon-database"></span> Restart Databases</a></confirm-dropdown>
                    </li>
                    <li>
                        <confirm-dropdown dispatch="user_site_services/restartWorkers" :params="site.id"><a href="#"><span class="icon-worker"></span> Restart Workers</a></confirm-dropdown>
                    </li>
                </ul>

            </drop-down>
        </div>
    </h3>
</template>

<script>
    export default {
        data() {
          return {
              webhook : false,
              sshKey: false,
          }
        },
        methods: {
            createDeployHook() {
                return this.$store.dispatch('user_site_deployments/createDeployHook', this.$route.params.site_id)
            },
            removeDeployHook() {
                this.$store.dispatch('user_site_deployments/removeDeployHook', {
                    site : this.$route.params.site_id,
                    hook : this.site.automatic_deployment_id
                });
            },
        },
        computed: {
            site() {
                return this.$store.state.user_sites.site;
            },
            siteServers() {
                let siteServers = _.get(this.$store.state.user_site_servers.servers, this.$route.params.site_id)

                if(siteServers && siteServers.length) {
                    return siteServers
                }
            },
            deployHook() {
                if(this.site) {
                    return location.protocol+'//'+location.hostname + Vue.action('WebHookController@deploy', { siteHashID : this.site.hash })
                }
            },
        }
    }
</script>