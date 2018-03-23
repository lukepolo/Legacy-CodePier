<template>
    <div v-if="site">
        <div class="flex">
            <div class="flex--grow">
                <h2 class="heading">Site Overview
                </h2>
            </div>
            <div class="heading--btns">
                <confirm
                    confirm_class="btn-link"
                    confirm_position="bottom"
                    message="Slack Notifications"
                    confirm_btn="btn-primary"
                >
                    <tooltip message="Notifications" placement="bottom">
                        <span class="icon-notifications"></span>
                    </tooltip>
                    <div slot="form">
                        <p v-if="!hasNotificationProviders"><router-link :to="{ name : 'user_notification_providers' }">Connect your slack account</router-link> to receive site notifications.</p>

                        <template v-if="hasNotificationProviders">
                            <p>Enter the channel name you want CodePier to send notifications. By default, notifications are sent to #sites.</p>

                            <div class="flyform--group">
                                <div class="flyform--group-prefix">
                                    <input type="text" name="deployments" v-model="notificationChannelsForm.site" placeholder=" " value="#sites">
                                    <label>Site Channel</label>
                                    <div class="flyform--group-prefix-label">#</div>
                                </div>
                            </div>

                            <div class="flyform--group">
                                <div class="flyform--group-prefix">
                                    <input type="text" name="lifelines" v-model="notificationChannelsForm.servers" placeholder=" " value="#sites">
                                    <label>Servers Channel</label>
                                    <div class="flyform--group-prefix-label">#</div>
                                </div>
                            </div>

                            <div class="flyform--group">
                                <div class="flyform--group-prefix">
                                    <input type="text" name="status" v-model="notificationChannelsForm.lifelines" placeholder=" " value="#sites">
                                    <label>Lifeline Channel</label>
                                    <div class="flyform--group-prefix-label">#</div>
                                </div>
                            </div>
                        </template>
                    </div>
                    <button slot="confirm-button" @click="updateNotificationChannels" class="btn btn-small btn-primary">Update Channels</button>
                </confirm>

                <confirm
                    dispatch="user_sites/renameSite"
                    :params="{
                        site : site.id,
                        pile_id : site.pile_id,
                        domain : renameForm.domain,
                        wildcard_domain : renameForm.wildcard_domain,
                    }"
                    confirm_class="btn-link"
                    confirm_position="bottom"
                    message="Update Site Details"
                    confirm_btn="btn-primary"
                >
                    <tooltip message="Update Site Details" placement="bottom">
                        <span class="icon-pencil"></span>
                    </tooltip>
                    <div slot="form">

                        <div class="flyform--group-checkbox">
                            <label>
                                <input v-model="renameForm.wildcard_domain" type="checkbox" name="wildcard_domain">
                                <span class="icon"></span>
                                Wildcard Domain
                                <tooltip :message="'If your site requires wildcard for sub domains'" size="medium">
                                    <span class="fa fa-info-circle"></span>
                                </tooltip>
                            </label>
                        </div>

                        <div class="flyform--group">
                            <input v-model="renameForm.domain" type="text" name="domain" placeholder=" ">
                            <label for="domain">Domain</label>
                        </div>
                    </div>
                </confirm>

                <delete-site :site="site"></delete-site>

                <router-link :class="{ 'btn-disabled' : !siteActionsEnabled }" class="btn btn-primary" :to="{ name: 'site_repository', params : { site_id : site.id } }">Manage Site &nbsp;<span class="icon-arrow-right"></span> </router-link>
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
                        <span class="icon-refresh2 text-link" @click="getDns(true)"></span>
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

            <drop-down tag="span" v-if="!site.user_repository_provider_id">
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
                                <a @click.prevent href="#"><span class="fa fa-refresh"></span></a>
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
                                <a @click.prevent href="#"><span class="fa fa-refresh"></span></a>
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

            <router-link :to="{ name : 'site_backups', params : { site : this.site.id } }" tag="div" class="grid--item">
                <div class="providers--item">
                    <div class="providers--item-header">
                        <div class="providers--item-icon">
                            <span class="icon-database"></span>
                        </div>
                    </div>
                    <div class="providers--item-footer">
                        <div class="providers--item-footer-connect">
                            <h4>Database Backups</h4>
                        </div>
                    </div>
                </div>
            </router-link>
        </div>

        <div class="grid-2 grid-gap-large">
            <div class="grid--item">
                <h3 class="text-center heading">Recent Deployments</h3>

                <div v-if="!recentDeployments">
                    <div class="placeholder text-center">Recent deployments will show up here once you have deployed your site.</div>
                </div>
                <template v-else>
                    <div class="list">
                    <template v-for="recentDeployment in recentDeployments">
                        <div class="list--item list--item-icons">
                            <div>
                                {{ recentDeployment.status }} <time-ago :time="recentDeployment.created_at"></time-ago>

                                <div>
                                    <small>took ({{ diff(recentDeployment.created_at, recentDeployment.updated_at) }})</small>
                                </div>
                            </div>

                            <confirm dispatch="user_site_deployments/rollback" confirm_position="btns-only" confirm_class="btn-link" :params="{ siteDeployment : recentDeployment.id, site : site.id } " v-if="recentDeployment.status === 'Completed'">
                                <tooltip message="Rollback">
                                    <span class="icon-refresh2"></span>
                                </tooltip>
                            </confirm>
                        </div>


                        <div class="flex deployment">
                            <div class="flex--grow deployment--text">
                            </div>
                            <div class="deployment--btns">
                            </div>
                        </div>
                    </template>
                    </div>
                </template>
            </div>

            <life-lines></life-lines>

            <div class="grid-2 grid-gap-large">
                <div class="grid--item">
                    <h3 class="text-center heading">Backups</h3>
                    <p v-for="backup in backups">
                        {{ backup.name }} - {{ backup.type }} <button class="btn btn-default" @click="downloadBackup(backup)">Download</button>
                    </p>
                </div>
            </div>

        </div>
    </div>
</template>

<script>
import LifeLines from "./../components/Lifelines";
import DeleteSite from "./../components/DeleteSite";

export default {
  data() {
    return {
      showSlackForm: false,
      webhook: false,
      sshKey: false,
      notificationChannelsForm: this.createForm({
        site: null,
        server: null,
        lifelines: null
      }),
      renameForm: {
        domain: null,
        wildcard_domain : null
      }
    };
  },
  components: {
    LifeLines,
    DeleteSite
  },
  created() {
    this.fetchData();
  },
  watch: {
    $route: "fetchData",
    site: function(site) {
      Vue.set(this.renameForm, "domain", site.name);
      Vue.set(this.renameForm, "wildcard_domain", site.wildcard_domain);
    }
  },
  methods: {
    createDeployHook() {
      return this.$store.dispatch(
        "user_site_deployments/createDeployHook",
        this.$route.params.site_id
      );
    },
    removeDeployHook() {
      this.$store
        .dispatch("user_site_deployments/removeDeployHook", {
          site: this.$route.params.site_id,
          hook: this.site.automatic_deployment_id
        })
        .catch(() => {
          this.site.automatic_deployment_id = false;
        });
    },
    fetchData() {
      this.getDns();
      this.$store.dispatch("user_notification_providers/get");
      this.$store.dispatch(
        "user_site_deployments/get",
        this.$route.params.site_id
      );
      this.$store.dispatch(
        "user_site_schemaBackups/get",
        this.$route.params.site_id
      );
      Vue.set(this.renameForm, "domain", this.site ? this.site.name : null);
      Vue.set(this.renameForm, "wildcard_domain", this.site ? this.site.wildcard_domain : null);
    },
    getDns(refresh) {
      let data = {
        site: this.$route.params.site_id
      };

      if (refresh) {
        data.refresh = true;
      }

      this.$store.dispatch("user_site_dns/get", data);
    },
    updateNotificationChannels() {
      this.$store
        .dispatch("user_sites/updateNotificationChannels", {
          site: this.$route.params.site_id,
          slack_channel_preferences: this.notificationChannelsForm.data()
        })
        .then(() => {
          this.showSlackForm = false;
        });
    },
    resetForm() {
      this.notificationChannelsForm.reset();
      this.showSlackForm = false;
    },
    downloadBackup(backup) {
      this.$store.dispatch("user_site_schemaBackups/download", {
        backup: backup.id,
        site: this.$route.params.site_id
      });
    }
  },
  computed: {
    site() {
      let site = this.$store.state.user_sites.site;
      if (site && site.slack_channel_preferences) {
        Vue.set(
          this,
          "notificationChannelsForm",
          this.createForm(_.cloneDeep(site.slack_channel_preferences))
        );
      }
      return site;
    },
    backups() {
      return this.$store.state.user_site_schemaBackups.backups;
    },
    siteServers() {
      let siteServers = _.get(
        this.$store.state.user_site_servers.servers,
        this.$route.params.site_id
      );

      if (siteServers && siteServers.length) {
        return siteServers;
      }
    },
    deployHook() {
      if (this.site) {
        return (
          location.protocol +
          "//" +
          location.hostname +
          Vue.action("WebHookController@deploy", { siteHashId: this.site.hash })
        );
      }
    },
    dns() {
      return this.$store.state.user_site_dns.dns;
    },
    siteServers() {
      return _.get(
        this.$store.state.user_site_servers.servers,
        this.$route.params.site_id
      );
    },
    dnsIsPointedToServer() {
      if (this.siteServers && this.dns.ip) {
        return _.indexOf(_.map(this.siteServers, "ip"), this.dns.ip) > -1;
      }
    },
    recentDeployments() {
      return _.slice(this.$store.state.user_site_deployments.deployments, 0, 5);
    },
    hasNotificationProviders() {
      return this.$store.state.user_notification_providers.providers.length > 0
        ? true
        : false;
    }
  }
};
</script>
