<template>

    <section id="left" class="section-column" v-if="hasSites">

        <h3 class="section-header">
            <template v-if="currentPile.name && currentPile.name.length > 17">
                <tooltip :message="currentPile.name" placement="bottom-right">
                    <span class="text-clip">{{ currentPile.name }}</span>
                </tooltip>
            </template>
            <template v-else>
                <span class="text-clip">{{ currentPile.name }}</span>
            </template>

            Sites

            <span class="icon-web"></span>
        </h3>

        <div class="section-content">

            <div class="site-container">

                <div class="site" v-for="site in sites">
                    <site :site="site"></site>
                </div>

                <site-form :pile="currentPile.id"></site-form>
                
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

    </section>

</template>

<script>

    import SiteForm from './SiteForm.vue'
    import Site from './left-nav-components/Site.vue'

    export default {
        components: {
            Site,
            SiteForm
        },
        methods: {
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
