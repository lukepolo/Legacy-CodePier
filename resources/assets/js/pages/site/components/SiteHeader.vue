<template>
    <h3 class="section-header primary" v-if="site">
        <back></back>

        <a :href="'http://'+site.domain" target="_blank">
            {{ site.name }}
        </a>

        <div class="section-header--btn-right">
            <template v-if="site.public_ssh_key">
                <span class="dropdown">
                    <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
                        <span class="icon-web"></span>
                    </button>

                    <div class="dropdown-menu nowrap">
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
                </span>
            </template>
            <template v-if="deployHook">
                <span class="dropdown">
                    <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
                        <span class="icon-webhooks"></span>
                    </button>

                    <div class="dropdown-menu nowrap">
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
                </span>
            </template>

            <template v-if="siteServers">
                <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                    <span class="icon-server"></span>
                </button>

                <ul class="dropdown-menu nowrap dropdown-list">
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
            </template>
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
        methods() {

        },
        computed: {
            site() {
                return this.$store.state.sitesStore.site;
            },
            siteServers() {
                return _.get(this.$store.state.sitesStore.site_servers, this.$route.params.site_id)
            },
            deployHook() {
                if(this.site) {
                    return location.protocol+'//'+location.hostname + Vue.action('WebHookController@deploy', { siteHashID : this.site.hash })
                }
            },
        }
    }
</script>