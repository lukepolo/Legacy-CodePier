<style>
    #drag {
        position: absolute;
        top: 0;
        bottom: 0;
        height: 10px;
        width: 100%;
        cursor: ns-resize;
    }
</style>
<template>
    <footer v-watch-scroll="events_pagination" v-resizeable>
        <div id="drag"></div>
        <div class="header">
            <h4>
                <a class="toggle collapsed" data-toggle="collapse" href="#collapseEvents">
                    <span class="icon-warning"></span> Events
                </a>
            </h4>
        </div>

        <div class="collapse" id="collapseEvents">
            <ul class="filter">
                <li>
                    <span>Event Filters</span>
                </li>
                <li class="dropdown">
                    <a href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true" class="dropdown-toggle">
                        <strong>Pile:</strong> <span class="filter-selection">Dev</span> <span class="icon-arrow-up"></span>
                    </a>

                    <ul class="dropdown-menu dropup">
                        <div class="jcf-form-wrap">
                            <form>
                                <div class="jcf-input-group input-checkbox">
                                    <label class="select-all">
                                        <input type="checkbox">
                                        <span class="icon"></span>
                                        Select All
                                    </label>
                                    <label>
                                        <input type="checkbox">
                                        <span class="icon"></span>
                                        Dev
                                    </label>
                                    <label>
                                        <input type="checkbox">
                                        <span class="icon"></span>
                                        QA
                                    </label>
                                    <label>
                                        <input type="checkbox">
                                        <span class="icon"></span>
                                        Prod
                                    </label>
                                </div>
                            </form>

                            <div class="btn-footer">
                                <a class="btn btn-small">Cancel</a>
                                <a class="btn btn-small btn-primary">Apply</a>
                            </div>

                        </div>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true" class="dropdown-toggle">
                        <strong>Site:</strong> <span class="filter-selection">All Sites</span> <span class="icon-arrow-up"></span>
                    </a>

                    <ul class="dropdown-menu dropup">
                        <div class="jcf-form-wrap">
                            <form>
                                <div class="jcf-input-group input-checkbox">
                                    <label class="select-all">
                                        <input type="checkbox">
                                        <span class="icon"></span>
                                        Select All
                                    </label>
                                    <label>
                                        <input type="checkbox">
                                        <span class="icon"></span>
                                        dev.lukepolo.com
                                    </label>
                                    <label>
                                        <input type="checkbox">
                                        <span class="icon"></span>
                                        lukepolo.com
                                    </label>
                                    <label>
                                        <input type="checkbox">
                                        <span class="icon"></span>
                                        dev.jfalotico.com
                                    </label>
                                    <label>
                                        <input type="checkbox">
                                        <span class="icon"></span>
                                        jfalotico.com
                                    </label>
                                </div>
                            </form>

                            <div class="btn-footer">
                                <a class="btn btn-small">Cancel</a>
                                <a class="btn btn-small btn-primary">Apply</a>
                            </div>

                        </div>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true" class="dropdown-toggle">
                        <strong>Server:</strong> <span class="filter-selection">All Servers</span> <span class="icon-arrow-up"></span>
                    </a>

                    <ul class="dropdown-menu dropup">
                        <div class="jcf-form-wrap">
                            <form>
                                <div class="jcf-input-group input-checkbox">
                                    <label class="select-all">
                                        <input type="checkbox">
                                        <span class="icon"></span>
                                        Select All
                                    </label>
                                    <label>
                                        <input type="checkbox">
                                        <span class="icon"></span>
                                        Server 1
                                    </label>
                                    <label>
                                        <input type="checkbox">
                                        <span class="icon"></span>
                                        Server 2
                                    </label>
                                    <label>
                                        <input type="checkbox">
                                        <span class="icon"></span>
                                        Server 3
                                    </label>
                                    <label>
                                        <input type="checkbox">
                                        <span class="icon"></span>
                                        Server 4
                                    </label>
                                </div>
                            </form>

                            <div class="btn-footer">
                                <a class="btn btn-small">Cancel</a>
                                <a class="btn btn-small btn-primary">Apply</a>
                            </div>

                        </div>
                    </ul>
                </li>

                <li class="dropdown">
                    <a href="#" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="true" class="dropdown-toggle">
                        <strong>Event Type:</strong> <span class="filter-selection">Deployments</span> <span class="icon-arrow-up"></span>
                    </a>

                    <ul class="dropdown-menu dropup">
                        <div class="jcf-form-wrap">
                            <form>
                                <div class="jcf-input-group input-checkbox">
                                    <label class="select-all">
                                        <input type="checkbox">
                                        <span class="icon"></span>
                                        Select All
                                    </label>
                                    <label>
                                        <input type="checkbox">
                                        <span class="icon"></span>
                                        Deployments
                                    </label>
                                    <label>
                                        <input type="checkbox">
                                        <span class="icon"></span>
                                        Something Else
                                    </label>
                                    <label>
                                        <input type="checkbox">
                                        <span class="icon"></span>
                                        Blah Blah
                                    </label>
                                    <label>
                                        <input type="checkbox">
                                        <span class="icon"></span>
                                        Woo
                                    </label>
                                </div>
                            </form>

                            <div class="btn-footer">
                                <a class="btn btn-small">Cancel</a>
                                <a class="btn btn-small btn-primary">Apply</a>
                            </div>

                        </div>
                    </ul>
                </li>

            </ul>
            <div class="events-container">


                <!--&lt;!&ndash; TODO Add in for when there are no events &ndash;&gt;-->
                <!--&lt;!&ndash;<div class="event-none">&ndash;&gt;-->
                    <!--&lt;!&ndash;Get going!&ndash;&gt;-->
                <!--&lt;!&ndash;</div>&ndash;&gt;-->

                <!--&lt;!&ndash;<template v-for="server in servers">&ndash;&gt;-->
                    <!--&lt;!&ndash;<server-event :server="server"></server-event>&ndash;&gt;-->
                <!--&lt;!&ndash;</template>&ndash;&gt;-->
                <!--<div class="event">-->
                <!--<div class="event-status event-status-success"></div>-->
                <!--<div class="event-name">Deployment Successful</div>-->
                <!--<div class="event-pile"><span class="icon-layers"></span> Dev</div>-->
                <!--<div class="event-site"><span class="icon-server"></span> jfalotico.com</div>-->
                <!--</div>-->
                <!--<div class="event">-->
                <!--<div class="event-status event-status-success"></div>-->
                <!--<div class="event-name">Site Installed</div>-->
                <!--<div class="event-pile"><span class="icon-layers"></span> Dev</div>-->
                <!--<div class="event-site"><span class="icon-browser"></span> jfalotico.com</div>-->
                <!--</div>-->
                <!--<div class="event">-->
                    <!--<div class="event-status event-status-error"></div>-->
                    <!--<div class="event-name">-->
                        <!--<a class="collapsed" data-toggle="collapse" href="#collapseError1">-->
                            <!--<span class="icon-play"></span>-->
                        <!--</a> Deployment Failed-->
                        <!--<div class="event-details collapse" id="collapseError1">-->
                            <!--<span class="text-error">-->
                                <!--<strong>ERROR STUFF HERE.</strong>-->
                                <!--Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.-->
                            <!--</span>-->
                        <!--</div>-->
                    <!--</div>-->
                    <!--<div class="event-pile"><span class="icon-layers"></span> Dev</div>-->
                    <!--<div class="event-site"><span class="icon-browser"></span> jfalotico.com</div>-->
                <!--</div>-->
                <!--<div class="event">-->
                <!--<div class="event-status event-status-warning"></div>-->
                <!--<div class="event-name">Deployment Started</div>-->
                <!--<div class="event-pile"><span class="icon-layers"></span> Dev</div>-->
                <!--<div class="event-site"><span class="icon-browser"></span> jfalotico.com</div>-->
                <!--</div>-->
                <!--<div class="event">-->
                <!--<div class="event-status event-status-success"></div>-->
                <!--<div class="event-name">Deployment Successful</div>-->
                <!--<div class="event-pile"><span class="icon-layers"></span> Dev</div>-->
                <!--<div class="event-site"><span class="icon-server"></span> jfalotico.com</div>-->
                <!--</div>-->
                <!--<div class="event">-->
                <!--<div class="event-status event-status-success"></div>-->
                <!--<div class="event-name">Site Installed</div>-->
                <!--<div class="event-pile"><span class="icon-layers"></span> Dev</div>-->
                <!--<div class="event-site"><span class="icon-server"></span> jfalotico.com</div>-->
                <!--</div>-->
                <!--<div class="event">-->
                <!--<div class="event-status event-status-error"></div>-->
                <!--<div class="event-name">Deployment Failed</div>-->
                <!--<div class="event-pile"><span class="icon-layers"></span> Dev</div>-->
                <!--<div class="event-site"><span class="icon-browser"></span> jfalotico.com</div>-->
                <!--</div>-->
                <!--<div class="event">-->
                <!--<div class="event-status event-status-warning"></div>-->
                <!--<div class="event-name">Deployment Started</div>-->
                <!--<div class="event-pile"><span class="icon-layers"></span> Dev</div>-->
                <!--<div class="event-site"><span class="icon-browser"></span> jfalotico.com</div>-->
                <!--</div>-->

                <section v-for="event in events">
                    <div class="event">
                        <div class="event-status" :class="{'event-status-neutral' : !event.status, 'event-status-success' : event.status == 'Completed', 'event-status-error' : event.status == 'Failed' }"></div>
                        <div class="event-name">
                            <a class="collapsed" data-toggle="collapse" :href="'#' + event.id">
                                <span class="icon-play"></span>
                            </a> Deployment

                            <a target="_blank" :href="'https://'+ event.site.user_repository_provider.repository_provider.url + '/' + event.site.repository + '/commit/' + event.git_commit">view commit</a>

                            <div class="event-details collapse" :id="event.id">

                                <template v-for="server_deployment in event.server_deployments">

                                    <div class="event-status" :class="{'event-status-neutral' : (! server_deployment.failed && ! server_deployment.completed), 'event-status-success' : server_deployment.completed, 'event-status-error' : server_deployment.failed }"></div>
                                    <a class="collapsed" data-toggle="collapse" :href="'#' + event.id + '_server_deployment_' + server_deployment.server.id">
                                        <span class="icon-play"></span>
                                    </a>

                                    {{ server_deployment.server.name }} ({{ server_deployment.server.ip }}) - {{ server_deployment.status }}

                                    <div class="event-details collapse" :id="event.id + '_server_deployment_' + server_deployment.server.id">

                                        <ul>
                                            <template v-for="deployment_event in server_deployment.events">
                                                <li>

                                                    <div class="event-status" :class="{'event-status-neutral' : (! deployment_event.failed && ! deployment_event.completed), 'event-status-success' : deployment_event.completed, 'event-status-error' : deployment_event.failed }"></div>
                                                    <a class="collapsed" :class="{ 'in' : deployment_event.failed }" data-toggle="collapse" :href="'#deployment_event_' + deployment_event.id" v-if="deployment_event.log && filterArray(deployment_event.log).length">
                                                        <span class="icon-play"></span>
                                                    </a>

                                                    {{ deployment_event.step.step }}
                                                    <template v-if="deployment_event.completed">
                                                        took {{ formatSeconds(deployment_event.runtime) }} seconds
                                                    </template>

                                                    <div class="event-details collapse" :class="{ 'in' : deployment_event.failed, 'out' : !deployment_event.failed }" :id="'deployment_event_'+deployment_event.id">
                                                        <pre v-for="log in filterArray(deployment_event.log)" v-if="deployment_event.log">{{ log }}</pre>
                                                    </div>
                                                </li>
                                            </template>
                                        </ul>

                                    </div>
                                </template>

                            </div>
                        </div>
                        <div class="event-pile"><span class="icon-layers"></span> {{ event.site.pile.name }}</div>
                        <div class="event-site"><span class="icon-browser"></span> {{ event.site.name }}</div>
                    </div>
                </section>
            </div>
        </div>

    </footer>
</template>

<script>

    import ServerEvent from './components/ServerEvent.vue';

    Vue.directive('resizeable', {
        inserted: function (el, bindings) {

            var isResizing = false,
                lastOffset = null,
                container = $('#app-layout'),
                top = $('#main'),
                bottom = $(el),
                handle = $('#drag');

            handle.on('mousedown', function (e) {
                isResizing = true;
            });

            $('#collapseEvents').on('hide.bs.collapse', function (el) {
                if($(el.target).attr('id') == 'collapseEvents') {
                    bottom.css('height', 'auto');
                }
            });

            $(document).on('mousemove', function (e) {
                if (!isResizing || !$('#collapseEvents').hasClass('in')) {
                    return;
                }

                lastOffset = container.height() - (e.clientY - container.offset().top);

                top.css('height', lastOffset);
                bottom.css('height', lastOffset);
            }).on('mouseup', function (e) {
                isResizing = false;
            });

        }
    });

    Vue.directive('watch-scroll', {
        update: function (el, bindings) {

//            $(el).unbind('scroll');
//
//            var pagination = bindings.value;
//
//            var nextPage = pagination.current_page + 1;
//            if (nextPage <= pagination.last_page) {
//                $(el).bind('scroll', function() {
//                    var $el = $(el);
//                    if (el.scrollHeight - $el.scrollTop() - $el.outerHeight() < 1) {
//                        eventStore.dispatch('getEvents', nextPage);
//                    }
//                });
//            }
        }
    });

    export default {
        components : {
            ServerEvent
        },
        created() {
            this.fetchData();
        },
        methods: {
            filterArray(data) {
                if(Array.isArray(data)) {
                    return data.filter(String);
                }
              return [];
            },
            fetchData() {

                var store = this.$store;

                store.dispatch('getServers', function () {
                    _(store.state.serversStore.servers).forEach(function (server) {

                        if(server.progress < 100) {
                            store.dispatch('getServersCurrentProvisioningStep', server.id)
                        }
//
                        Echo.private('App.Models.Server.Server.' + server.id)
                                .listen('Server\\ServerProvisionStatusChanged', (data) => {
                                    store.commit("UPDATE_SERVER", data.server)
                                    store.commit("SET_SERVERS_CURRENT_PROVISIONING_STEP", [data.server.id, data.serverCurrentProvisioningStep]);
                                })
                    });
                });

                store.dispatch('getSites', function () {
                    _(store.state.sitesStore.sites).forEach(function (site) {
                        Echo.private('App.Models.Site.Site.' + site.id)
                                .listen('Site\\DeploymentStepStarted', (data) => {
                                    console.info('NEED TO DO STARTED EVENT');
                                })
                                .listen('Site\\DeploymentStepCompleted', (data) => {
                                    store.commit('UPDATE_DEPLOYMENT_EVENT', data);
                                })
                                .listen('Site\\DeploymentStepFailed', (data) => {
                                    console.info('NEED TO DO FAILED EVENT');
                                })
                                .listen('Site\\DeploymentCompleted', (data) => {
                                    console.info('NEED TO DO COMPLETED EVENT');
                                })
                                .notification((notification) => {
                                    if(notification.type == 'App\\Notifications\\Site\\NewSiteDeployment') {
                                       store.commit('ADD_NEW_SITE_DEPLOYMENT', notification.siteDeployment);
                                    }
                                });

                    });
                });

                store.dispatch('getEvents');
            },
            formatSeconds(number) {
                return parseFloat(number).toFixed(2);
            }
        },
        computed: {
            servers() {
                return this.$store.state.serversStore.servers;
            },
            events() {
                return this.$store.state.eventsStore.events;
            },
            events_pagination() {
                return this.$store.state.eventsStore.events_pagination;
            }
        },
    }

</script>