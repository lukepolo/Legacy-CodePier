<template>
    <div v-if="site">

        <h1>Site Overview</h1>

        <confirm dispatch="user_sites/destroy" :params="site.id" :confirm_with_text="site.name"> Delete Site </confirm>
        


        <router-link class="btn btn-primary" :to="{ name: 'site_repository', params : { site_id : site.id } }">Manage</router-link>

        <div>
            <h3>
                Repository / Deploy Branch
            </h3>
            <p>
                {{ site.repository }} @ {{ site.branch }}
            </p>
        </div>

        <div>
            <h3>Recent deployments</h3>
            <template v-for="deploymentEvent in deploymentEvents">
                {{ deploymentEvent.status }} {{ timeAgo(deploymentEvent.created_at) }}
                <br>
                <small>
                    took ({{ diff(deploymentEvent.created_at, deploymentEvent.updated_at) }})
                </small>
                <br>
            </template>
            <template v-for="recentDeployment in recentDeployments">
                {{ recentDeployment.status }} {{ timeAgo(recentDeployment.created_at) }}
                <br>
                <small>
                    took ({{ diff(recentDeployment.created_at, recentDeployment.updated_at) }})
                </small>

                <confirm dispatch="user_site_deployments/rollback" confirm_class="btn btn-small" :params="{ siteDeployment : recentDeployment.id, site : site.id } " v-if="recentDeployment.status === 'Completed'">
                    Rollback
                </confirm>
                <br>
            </template>
        </div>

        <br>
        <div>
            <h3>
                Recent Commands
                <small>
                    coming soon!
                </small>
            </h3>
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


        <div>
            <form @submit.prevent="createLifeline">
                Name : <input type="text" v-model="lifeline_form.name">
                <br>
                Should check-in every <input type="text" v-model="lifeline_form.threshold"> minutes
                <br>
                <button type="submit">Create Life Line</button>
            </form>

            <p v-for="lifeLine in lifeLines">
                {{ lifeLine.name }} - {{ lifeLine.url }}<br>
                <template v-if="lifeLine.last_seen">
                    {{ timeAgo(lifeLine.last_seen) }}
                </template>
                <template v-else>
                    Never Seen
                </template>
                <br>
                <confirm dispatch="user_site_life_lines/destroy" confirm_class="btn btn-small" :params="{ site : site.id, life_line : lifeLine.id }">
                    delete
                </confirm>

            </p>

        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                lifeline_form: this.createForm({
                    name : null,
                    threshold : null,
                    site : this.$route.params.site_id
                })
            }
        },
        mounted() {
            this.fetchData()
        },
        watch: {
            '$route' : 'fetchData',
        },
        methods: {
            fetchData() {
                this.getDns()
                this.$store.dispatch('user_site_life_lines/get', this.$route.params.site_id)
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
            createLifeline() {
                this.$store.dispatch('user_site_life_lines/store', this.lifeline_form)
            }
        },
        computed: {
            dns() {
                return this.$store.state.user_site_dns.dns
            },
            site() {
                return this.$store.state.user_sites.site
            },
            lifeLines() {
                return this.$store.state.user_site_life_lines.life_lines
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