<template>
    <section id="left" class="section-column" v-if="hasSites">
        <h3 class="section-header">{{ currentPile.name }} Sites</h3>

        <div class="section-content">
            <div class="site-container">
                <div class="site" v-for="site in sites">
                    <site :site="site"></site>
                </div>
                <div class="jcf-form-wrap">
                    <form @submit.prevent="saveSite" v-if="adding_site" class="floating-labels">
                        <div class="jcf-input-group">
                            <input name="domain" v-model="form.domain" type="text">
                            <label for="domain">
                                <span class="float-label">
                                    Domain / Alias
                                </span>
                            </label>
                        </div>

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

    import Site from './left-nav-components/Site.vue'

    export default {
        components: {
            Site
        },
        data () {
            return {
                adding_site: false,
                form: this.createForm({
                    domain: null,
                    pile_id: this.$store.state.user.user.current_pile_id
                })
            }
        },
        methods: {
            saveSite () {
                this.$store.dispatch('user_sites/store', this.form).then((site) => {
                    if (site) {
                        this.adding_site = false
                        this.form.reset()
                    }
                })
            },
            slackInviteLink () {
                return this.action('User\UserController@slackInvite')
            }
        },
        computed: {
            userSshKeys () {
                return this.$store.state.user_ssh_keys.ssh_keys
            },
            currentPile () {
                return this.getPile(this.$store.state.user.user.current_pile_id)
            },
            user () {
                return this.$store.state.user.user
            },
            sites () {
                return _.filter(this.$store.state.user_sites.sites, (site) => {
                    return site.pile_id === this.current_pile_id
                })
            },
            current_pile_id () {
                return this.$store.state.user.user.current_pile_id
            }
        }
    }
</script>
