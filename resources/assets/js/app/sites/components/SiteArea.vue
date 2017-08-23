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
                                <router-view name="subNav" :class="{
                                    'child-view' : workFlowCompleted === true,
                                    'in-workflow' : workFlowCompleted !== true
                                 }">

                                    <template v-if="workFlowCompleted !== true && totalWorkflowSteps > 0">
                                        <h2>{{ workFlowName }}</h2>
                                        <h4 class="heading secondary">Workflow Step #{{ workflowStepsCompleted }} / {{ totalWorkflowSteps }} </h4>
                                        <div class="alert-info" v-if="workflowMessage">{{ workflowMessage }}</div>
                                        <hr>
                                    </template>
                                    <router-view></router-view>
                                    <template v-if="workFlowCompleted !== true && totalWorkflowSteps > 0">
                                        <div class="flyform--footer">
                                            <div class="flyform--footer-btns">
                                                <button @click="revertWorkFlow" class="btn" :disabled="workflowStepsCompleted === 1" :class="{ disabled : workflowStepsCompleted === 1}">Previous</button>
                                                <button @click="updateWorkFlow" class="btn btn-primary">Continue</button>
                                            </div>
                                        </div>
                                    </template>

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
            'site'() {
                if(this.site && this.pileId !== this.site.pile_id) {
                    this.$store.dispatch('user_piles/change', this.site.pile_id).then(() => {
                        app.$router.push({
                            name : 'site_overview',
                            params : {
                                site_id : this.site.id
                            }
                        })
                    })
                }
                this.checkRedirect()
            }

        },
        methods: {
            checkRedirect() {

                let site = this.site

                if (site && site.id === parseInt(this.$route.params.site_id)) {

                    if(site.repository) {
                        if(this.workFlowCompleted !== true) {

                            if(site.workflow) {
                                if(this.workFlowCompleted !== this.$route.name) {

                                    console.info(this.workFlowCompleted)

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
                } else {
                    this.checkRedirect()
                }
            },
            revertWorkFlow() {
                let workflow = _.clone(this.site.workflow)

                let workflows = _.sortBy(
                    _.map(workflow, function(flow, step) {
                        flow.step = step
                        return flow
                    }),
                    'order'
                );

                let currentWorkflowIndex = _.findKey(workflows, function(flow) {
                    return flow.completed === false
                })

                workflow[workflows[currentWorkflowIndex - 1].step].completed = false

                this.updateFlow(workflow)
            },
            updateWorkFlow() {
                let workflow = _.clone(this.site.workflow)
                workflow[this.workFlowCompleted].completed = true
                this.updateFlow(workflow)
            },
            updateFlow(workflow) {
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
            pileId() {
                return this.$store.state.user.user.current_pile_id
            },
            site() {
                return this.$store.state.user_sites.site
            },
            workflowStepsCompleted() {
                return _.filter(this.site.workflow, function(flow) {
                    return flow.completed === true
                }).length + 1
            },
            totalWorkflowSteps() {
                let count = _.keys(this.site.workflow).length;
                if(this.site.workflow && this.site.workflow.message) {
                    count--;
                }

                return count;
            },
            workflowMessage() {
                if(this.site.workflow) {
                    return this.site.workflow.message
                }
            },
            notOverview() {
                return this.$route.name !== 'site_overview'
            },
            workFlowName() {
                return this.workFlowCompleted.replace('site_', '').replace('_', ' ')
            }
        }
    }
</script>