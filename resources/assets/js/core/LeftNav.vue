<template>
    <section id="left" class="section-column">
        <h3 class="section-header">{{ currentPile.name }} Sites</h3>

        <div class="section-content">
            <div class="site-container">
                <div class="site" v-for="site in sites">
                    <router-link :to="{ name: 'site_repository', params : { site_id : site.id} }">
                    <div class="site-name">
                        <tooltip
                            class="event-status"
                            :class="{
                                'event-status-neutral' : site.last_deployment_status == 'Queued',
                                'event-status-success' : site.last_deployment_status == 'Completed',
                                'event-status-error' : site.last_deployment_status == 'Failed',
                                'icon-spinner' : site.last_deployment_status == 'Running'
                            }"
                            :message="getDeploymentStatusText(site)"
                            placement="right"
                        >
                        </tooltip>
                        {{ site.name }}
                        <site-deploy :site="site"></site-deploy>
                    </div>
                </router-link>
                </div>
                <div class="jcf-form-wrap">
                    <form @submit.prevent="saveSite" v-if="adding_site" class="floating-labels">
                        <div class="jcf-input-group">
                            <input name="domain" v-model="form.domain" type="text">
                            <label for="domain">
                                <span class="float-label">
                                    <template v-if="!form.domainless">
                                        Domain
                                    </template>
                                    <template v-else>
                                        Alias
                                    </template>
                                </span>
                            </label>
                        </div>

                        <input type="checkbox" v-model="form.domainless"> Not a domain
                        <button class="btn btn-primary">Save</button>
                    </form>
                </div>

                <div class="btn-container text-center" v-if="current_pile_id">
                    <div @click="adding_site = !adding_site" class="btn" :class="{ 'btn-primary' : !adding_site}">
                        <template v-if="!adding_site">
                            Create Site
                        </template>
                        <template v-else>
                            Cancel
                        </template>

                    </div>
                </div>

                <div class="slack-invite" v-if="userSshKeys && !userSshKeys.length">
                    <router-link :to="{ name : 'user_ssh_keys' }">
                        Create A SSH Key
                        <div class="small">You have not created an account ssh key</div>
                    </router-link>
                </div>

                <div class="slack-invite" v-if="!user.invited_to_slack">
                    <a :href="slackInviteLink()">
                        <i class="fa fa-slack" aria-hidden="true"></i>
                        Get Invite to Slack
                    </a>
                </div>
            </div>

        </div>

    </section>
</template>

<script>
    import SiteDeploy from './components/SiteDeploy.vue'
    export default {
        components : {
            SiteDeploy
        },
        created() {
            this.$store.dispatch('getSites');
            this.$store.dispatch('getUserSshKeys');
        },
        data() {
            return {

                adding_site: false,
                form: {
                    domain: null,
                    domainless: false,
                }
            }
        },
        methods: {
            saveSite() {
                this.$store.dispatch('createSite', this.form).then((site) => {
                    if(site) {
                        this.adding_site = false;
                    }
                });

            },
            getDeploymentStatusText(site) {

                switch(site.last_deployment_status) {
                    case 'Completed':
                        return 'All Good'
                        break;
                    case 'Failed' :
                        return 'Something Failed'
                        break;
                    case 'Queued' :
                        return 'Queued'
                        break;
                    default :
                        return 'Deploying'
                        break;
                }
            },
            slackInviteLink() {
                return this.action('User\UserController@slackInvite')
            }
        },
        computed: {
            userSshKeys() {
                return this.$store.state.userSshKeysStore.user_ssh_keys;
            },
            currentPile() {
                return this.getPile(this.$store.state.userStore.user.current_pile_id);
            },
            user() {
                return this.$store.state.userStore.user;
            },
            sites() {
                return this.$store.state.sitesStore.sites;
            },
            current_pile_id() {
                return this.$store.state.userStore.user.current_pile_id;
            }
        }
    }
</script>