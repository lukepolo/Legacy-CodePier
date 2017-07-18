<template>
    <div v-if="site">

        <h2 class="heading">Site Overview</h2>

        <div class="flyform--group">
            <label>Repository @ Deploy Branch</label>
            {{ site.repository }} @ {{ site.branch }}
        </div>



        <hr>

        <div class="grid-2">
            <div class="grid--item">
                <h3 class="text-center">Recent Deployments</h3>

                <template v-for="deploymentEvent in deploymentEvents">
                    {{ deploymentEvent.status }} <time-ago :time="deploymentEvent.created_at"></time-ago>
                    <br>
                    <small>
                        took ({{ diff(deploymentEvent.created_at, deploymentEvent.updated_at) }})
                    </small>
                    <br>
                </template>
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

                <hr>
                <h3 class="heading text-center">Recent Commands</h3>
                <h3><small><em>coming soon!</em></small></h3>
            </div>

            <life-lines></life-lines>
        </div>

        <br><br><hr>

        <confirm dispatch="user_sites/destroy" :params="site.id" :confirm_with_text="site.name"> Delete Site </confirm>
        


        <router-link class="btn btn-primary" :to="{ name: 'site_repository', params : { site_id : site.id } }">Manage</router-link>



        <br>
        <div>

        </div>

        <br>

        <div>
            <h3>
                DNS
                <i class="fa fa-refresh" @click="getDns(true)"></i>
            </h3>
            <template v-if="dns.host">
                Your site is pointed to :
                <span :class="{ 'text-error' : !dnsIsPointedToServer , 'text-success' : dnsIsPointedToServer}">
                    {{ dns.ip }}
                </span>
            </template>
            <template v-else>
                Cannot find DNS entry
            </template>
        </div>


    </div>
</template>

<script>
    import LifeLines from './../components/Lifelines'
    export default {
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
            fetchData() {
                this.getDns()
                this.$store.dispatch('user_site_deployments/get', this.$route.params.site_id)
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
            dns() {
                return this.$store.state.user_site_dns.dns
            },
            site() {
                return this.$store.state.user_sites.site
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
                return this.$store.state.user_site_deployments.deployments
            },
            deploymentEvents() {
                if(this.site && this.recentDeployments) {

                    let latestDeployment = this.recentDeployments[0]

                    return _.filter(this.$store.state.events.events, (event) => {
                        return event.event_type === 'App\\Models\\Site\\SiteDeployment' &&
                            event.site_id ===  this.site.id &&
                            !_.find(this.recentDeployments, { id : event.id }) &&
                            (
                                !latestDeployment ||
                                (latestDeployment.created_at) < event.created_at
                            )
                    })
                }

            }
        },
    }
</script>