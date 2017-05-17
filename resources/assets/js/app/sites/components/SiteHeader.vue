<template>
    <h3 class="section-header primary" v-if="site">
        <back></back>

        <a :href="'http://'+site.domain" target="_blank">
            {{ site.name }}
        </a>

        <div class="section-header--btn-right">

            <template v-if="site && !site.automatic_deployment_id">
                <span @click="createDeployHook" class="dropdown">
                    <tooltip message="Enable Auto Deploy" placement="bottom-left" class="btn btn-default">
                        <span class="icon-cloud-auto-deploy"></span>
                    </tooltip>
                </span>
            </template>
            <template v-else>
                <span class="dropdown">
                    <tooltip @click="removeDeployHook" message="Remove Auto Deploy" placement="bottom-left" class="btn btn-default">
                        <span class="icon-cloud-auto-deploy active"></span>
                    </tooltip>
                </span>
            </template>

            <drop-down tag="span">

                <tooltip slot="header" message="Site SSH Key" placement="bottom-left" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                    <span class="icon-web"></span>
                </tooltip>

                <div slot="content" class="dropdown-menu dropdown-content nowrap">
                    <div class="jcf-form-wrap">
                        <div class="jcf-input-group">
                            <div class="input-question">
                                <confirm-dropdown dispatch="refreshSshKeys" :params="site.id">Public SSH Key &nbsp;<a href="#"><span class="fa fa-refresh"></span></a></confirm-dropdown>
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
                                <confirm-dropdown dispatch="refreshDeployKey" :params="site.id">Deploy Hook URL &nbsp;<a href="#"><span class="fa fa-refresh"></span></a></confirm-dropdown>
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
                        <confirm-dropdown dispatch="restartSiteWebServices" :params="site.id"><a href="#"><span class="icon-web"></span> Restart Web Services</a></confirm-dropdown>
                    </li>
                    <li>
                        <confirm-dropdown dispatch="restartSiteServers" :params="site.id"><a href="#"><span class="icon-server"></span> Restart Servers</a></confirm-dropdown>
                    </li>
                    <li>
                        <confirm-dropdown dispatch="restartSiteDatabases" :params="site.id"><a href="#"><span class="icon-database"></span> Restart Databases</a></confirm-dropdown>
                    </li>
                    <li>
                        <confirm-dropdown dispatch="restartSiteWorkers" :params="site.id"><a href="#"><span class="icon-worker"></span> Restart Workers</a></confirm-dropdown>
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
                return this.$store.dispatch('createDeployHook', this.$route.params.site_id)
            },
            removeDeployHook() {
                this.$store.dispatch('removeDeployHook', {
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
                let siteServers = _.get(this.$store.state.user_site_servers.servers, this.$route.params.site_id);
                if(siteServers && _.keys(siteServers).length) {
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