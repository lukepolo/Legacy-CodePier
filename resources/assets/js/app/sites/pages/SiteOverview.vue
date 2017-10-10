<template>
    <div v-if="site">
        <div class="flex">
            <div class="flex--grow">
                <h2 class="heading">Site Overview</h2>
            </div>
            <div>
                <router-link class="btn btn-primary" :to="{ name: 'site_repository', params : { site_id : site.id } }">Manage Site &nbsp;<span class="icon-arrow-right"></span> </router-link>
            </div>
        </div>

        <br>

        <div class="grid-2">
            <div class="grid--item">
                <h3>Repository @ Deploy Branch</h3>
                {{ site.repository }} @ {{ site.branch }}
            </div>
            <div class="grid--item">
                <h3>DNS
                    <tooltip message="Refresh DNS">
                        <span class="icon-refresh text-link" @click="getDns(true)"></span>
                    </tooltip>
                </h3>

                <template v-if="dns.host">
                    Your domain is currently pointing to :
                    <a :href="'//' + dns.ip" target="_blank" :class="{ 'text-error' : !dnsIsPointedToServer , 'text-success' : dnsIsPointedToServer}">
                        {{ dns.ip }}
                    </a>
                </template>
                <template v-else>
                    Cannot find DNS entry
                </template>
            </div>
        </div>

        <br><br>

        <div class="providers grid-3">
            <template v-if="site.user_repository_provider_id">
                <template v-if="workFlowCompleted === true">

                    <template v-if="!site.automatic_deployment_id">
                        <div class="grid--item" @click="createDeployHook">
                            <div class="providers--item">
                                <div class="providers--item-header">
                                    <div class="providers--item-icon">
                                        <span class="icon-cloud-auto-deploy"></span>
                                    </div>
                                </div>
                                <div class="providers--item-footer">
                                    <div class="providers--item-footer-connect">
                                        <h4>Enable Auto Deploy</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <template v-else>
                        <div class="grid--item" @click="removeDeployHook">
                            <div class="providers--item">
                                <div class="providers--item-header">
                                    <div class="providers--item-icon">
                                        <span class="icon-cloud-auto-deploy active"></span>
                                    </div>
                                    <div class="providers--item-name">
                                        <span class="text-success">Active</span>
                                    </div>
                                </div>
                                <div class="providers--item-footer">
                                    <div class="providers--item-footer-connect">
                                        <h4>Disable Auto Deploy</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                </template>
            </template>

            <drop-down tag="span">
                <div class="grid--item" slot="header">
                    <div class="providers--item">
                        <div class="providers--item-header">
                            <div class="providers--item-icon">
                                <span class="icon-web"></span>
                            </div>
                        </div>
                        <div class="providers--item-footer">
                            <div class="providers--item-footer-connect">
                                <h4 class="providers--title">Site SSH Key</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div slot="content" class="dropdown-menu dropdown-content nowrap">
                    <h3>
                        <confirm-dropdown dispatch="user_site_ssh_keys/refreshPublicKey" :params="site.id">
                            Public SSH Key &nbsp;
                            <tooltip message="Refresh SSH Key">
                                <a href="#"><span class="fa fa-refresh"></span></a>
                            </tooltip>
                        </confirm-dropdown>
                    </h3>

                    <div class="flyform--group flyform--group-nomargin">
                        <textarea rows="5" readonly>{{ site.public_ssh_key }}</textarea>
                    </div>

                    <div class="text-right">
                        <tooltip message="Copy to Clipboard">
                            <clipboard :data="site.public_ssh_key"></clipboard>
                        </tooltip>
                    </div>

                </div>
            </drop-down>

            <drop-down tag="span">
                <div class="grid--item" slot="header">
                    <div class="providers--item">
                        <div class="providers--item-header">
                            <div class="providers--item-icon">
                                <span class="icon-webhooks"></span>
                            </div>
                        </div>
                        <div class="providers--item-footer">
                            <div class="providers--item-footer-connect">
                                <h4>Deploy Hook URL</h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div slot="content" class="dropdown-menu dropdown-content nowrap">
                    <h3>
                        <confirm-dropdown dispatch="user_site_deployments/refreshDeployKey" :params="site.id">
                            Deploy Hook URL &nbsp;
                            <tooltip message="Refresh Deploy Key">
                                <a href="#"><span class="fa fa-refresh"></span></a>
                            </tooltip>
                        </confirm-dropdown>
                    </h3>

                    <div class="flyform--group flyform--group-nomargin">
                        <textarea  rows="3" readonly :value="deployHook"></textarea>
                    </div>

                    <div class="text-right">
                        <tooltip message="Copy to Clipboard">
                            <clipboard :data="deployHook"></clipboard>
                        </tooltip>
                    </div>

                </div>
            </drop-down>
        </div>

        <div class="grid-2">
            <div class="grid--item">
                <h3 class="text-center">Recent Deployments</h3>

                <div v-if="!recentDeployments">
                    <div class="placeholder text-center">Recent deployments will show up here once you have deployed your site.</div>
                </div>
                <template v-else>
                    <template v-for="recentDeployment in recentDeployments">
                        {{ recentDeployment.status }} <time-ago :time="recentDeployment.created_at"></time-ago>
                        <br>
                        <small>
                            took ({{ diff(recentDeployment.created_at, recentDeployment.updated_at) }})
                        </small>

                        <confirm dispatch="user_site_deployments/rollback" confirm_class="btn btn-small" :params="{ siteDeployment : recentDeployment.id, site : site.id } " v-if="recentDeployment.status === 'Completed'">
                            Rollback
                        </confirm>
                        <br>
                    </template>
                </template>

                <!--<br>-->
                <!--<hr>-->
                <!--<h3 class="heading text-center">Recent Commands</h3>-->
                <!--<h3 class="text-center"><small><em>coming soon!</em></small></h3>-->
            </div>

            <life-lines></life-lines>
        </div>

        <div class="flyform--footer">
            <div class="flyform--footer-btns">
                <confirm dispatch="user_sites/destroy" :params="site.id" :confirm_with_text="site.name">Delete Site</confirm>
            </div>
        </div>

        <hr>

        <h3>Site Rename Form</h3>
        <form @submit.prevent>
            <div class="flyform--group">
                <input ref="domain" name="domain" v-model="renameForm.domain" type="text" placeholder=" ">
                <label for="domain">Domain / Alias</label>
            </div>

            <div class="flyform--footer">
                <div class="flyform--footer-btns">
                    <confirm
                        dispatch="user_sites/renameSite"
                        :params="{
                            site : this.site.id,
                            domain : this.renameForm.domain,
                        }"
                        :confirm_with_text="renameForm.domain"
                    >
                        Rename Site {{ renameForm.site }}
                    </confirm>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
    import LifeLines from './../components/Lifelines'
    export default {
        data() {
            return {
                webhook : false,
                sshKey: false,
                renameForm : {
                    domain : null,
                }
            }
        },
        components : {
            LifeLines
        },
        created() {
            this.fetchData()
        },
        watch: {
            '$route' : 'fetchData',
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
            fetchData() {
                this.getDns()
                this.$store.dispatch('user_site_deployments/get', this.$route.params.site_id)
                Vue.set(this.renameForm, 'domain', null)
            },
            getDns(refresh) {

                let data = {
                    site : this.$route.params.site_id,
                }

                if(refresh) {
                    data.refresh = true
                }

                this.$store.dispatch('user_site_dns/get', data)
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
            dns() {
                return this.$store.state.user_site_dns.dns
            },
            siteServers() {
                return _.get(this.$store.state.user_site_servers.servers, this.$route.params.site_id)
            },
            dnsIsPointedToServer() {
                if(this.siteServers && this.dns.ip) {
                    return _.indexOf(_.map(this.siteServers, 'ip'), this.dns.ip) > -1
                }
            },
            recentDeployments() {
                return _.slice(this.$store.state.user_site_deployments.deployments, 0, 5)
            }
        },
    }
</script>