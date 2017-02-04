<template>
    <h3 class="section-header primary" v-if="site">
        <back></back>

        {{ site.name }}

        <div class="pull-right">

            <div class="dropdown">


                <template v-if="!site.private">
                    <button class="btn btn-default btn-xs dropdown-toggle" @click="sshKey = !sshKey">
                        <span class="icon-web"></span>

                        <template v-if="sshKey">
                            <div class="jcf-form-wrap" style="position: absolute">
                                <div class="jcf-input-group">
                                    <div class="input-question">
                                        Public SSH Deploy Key:
                                    </div>
                                    <textarea rows="4" readonly>{{ site.public_ssh_key }}</textarea>
                                    <div class="text-right">
                                        <clipboard :data="site.public_ssh_key"></clipboard>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </button>
                </template>

                <button class="btn btn-default btn-xs dropdown-toggle" @click="webhook = !webhook">
                    <span class="icon-webhooks"></span>
                    <template v-if="webhook">
                        <div class="jcf-form-wrap" style="position: absolute">
                            <div class="jcf-input-group">
                                <div class="input-question">
                                    Deploy Hook URL :
                                </div>
                                <input type="text" readonly>{{ deployHook }}</input>
                                <div class="text-right">
                                    <clipboard :data="deployHook"></clipboard>
                                </div>
                            </div>
                        </div>
                    </template>
                </button>

                <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                    <span class="icon-server"></span>
                </button>
                <ul class="dropdown-menu nowrap">
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
            </div>
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
            deployHook() {
                if(this.site) {
                    return location.protocol+'//'+location.hostname + Vue.action('WebHookController@deploy', { siteHashID : this.site.hash })
                }
            },
        }
    }
</script>