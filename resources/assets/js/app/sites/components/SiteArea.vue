<template>

    <div class="parent">

        <left-nav></left-nav>

        <section id="middle" class="section-column">

            <site-header></site-header>

            <div class="section-content">

                <div class="container">

                    <template v-if="site">

                        <router-view name="nav" v-if="workFlowCompleted === true" ></router-view>

                        <transition :name="transitionName">

                            <template v-if="notOverview">
                                <router-view name="subNav" class="child-view">

                                    <template v-if="workFlowCompleted !== true && totalWorkflowSteps > 0">
                                        <h1>Workflow {{ workflowStepsCompleted }} / {{ totalWorkflowSteps }} </h1>
                                        <button @click="updateWorkFlow" class="btn btn-success">Continue</button>
                                    </template>
                                    <router-view></router-view>

                                </router-view>
                            </template>
                            <template v-else>
                                <router-view class="child-view"></router-view>
                            </template>

                        </transition>

                    </template>

                </div>

            </div>

        </section>

        <servers v-if="workFlowCompleted === true"></servers>

    </div>

</template>

<script>
    import SiteNav from './SiteNav.vue'
    import Servers from './Servers.vue'
    import SiteHeader from './SiteHeader.vue'
    import LeftNav from '../../../components/LeftNav.vue'

    export default {
        data() {
            return {
                transitionName : null
            }
        },
        components: {
            SiteNav,
            LeftNav,
            Servers,
            SiteHeader,
        },
        created() {
            this.fetchData()
        },
        watch: {
            '$route' (to, from) {

                let slide = 'slide-right'

                if(to.path.includes('setup')) {
                    slide = 'slide-right'
                }

                if(to.path.includes('server-setup')) {
                    slide = 'slide-left'
                }

                if(to.path.includes('security')) {
                    if(from.path.includes('server-setup')) {
                        slide = 'slide-right'
                    } else {
                        slide = 'slide-left'
                    }
                }

                if(from.path.includes('overview')) {
                    slide = 'slide-left'
                }

                this.transitionName = slide
                this.fetchData()
            },
            'site' : 'checkRedirect'

        },
        methods: {
            checkRedirect() {

                let site = this.site

                if (site && site.id === parseInt(this.$route.params.site_id)) {

                    if(site.repository) {
                        if(this.workFlowCompleted !== true) {

                            if(site.workflow) {
                                if(this.workFlowCompleted !== this.$route.name) {
                                    this.$router.push({ name: this.workFlowCompleted, params: { site_id: site.id }})
                                }

                            } else {
                                this.$router.push({ name: 'site_workflow', params: { site_id: site.id }})
                            }

                        } else if(this.$route.name === 'site_workflow') {
                            this.$router.push({ name: 'site_overview', params: { site_id: site.id }})
                        }
                    } else {
                        this.$router.push({ name: 'site_repository', params: { site_id: site.id }})
                    }
                }
            },
            fetchData() {
                let siteId = this.$route.params.site_id
                if(!this.site || this.site.id !== parseInt(siteId)) {
                    this.$store.dispatch('user_sites/show', siteId)
                }
                this.checkRedirect()
            },
            updateWorkFlow() {

                let workflow = _.clone(this.site.workflow)

                workflow[this.workFlowCompleted] = true

                this.$store.dispatch('user_sites/updateWorkflow', {
                    workflow : workflow,
                    site : this.$route.params.site_id,
                }).then(() => {
                    if(this.workFlowCompleted === true) {
                        this.$router.push({ name: 'site_overview', params: { site_id: this.site.id }})
                    }
                })
            }
        },
        computed: {
            site() {
                return this.$store.state.user_sites.site
            },
            workflowStepsCompleted() {
                return _.filter(this.site.workflow, function(status) {
                    return status === true
                }).length + 1
            },
            totalWorkflowSteps() {
                return _.keys(this.site.workflow).length
            },
            notOverview() {
                return this.$route.name !== 'site_overview'
            }
        }
    }
</script>