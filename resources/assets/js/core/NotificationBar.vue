<template>
    <footer v-watch-scroll="events_pagination">
        <div class="header">
            <h4>
                <a class="toggle" data-toggle="collapse" href="#collapseEvents">
                    <span class="icon-warning"></span> Events
                </a>
            </h4>
        </div>
        
        <div class="collapse" id="collapseEvents">
            <div class="events-container">
                <div class="event">
                    <div class="event-status event-status-success"></div>
                    <div class="event-name">Deployment Successful</div>
                    <div class="event-pile"><span class="icon-layers"></span> Dev</div>
                    <div class="event-site"><span class="icon-server"></span> jfalotico.com</div>
                </div>
                <div class="event">
                    <div class="event-status event-status-success"></div>
                    <div class="event-name">Site Installed</div>
                    <div class="event-pile"><span class="icon-layers"></span> Dev</div>
                    <div class="event-site"><span class="icon-browser"></span> jfalotico.com</div>
                </div>
                <div class="event">
                    <div class="event-status event-status-error"></div>
                    <div class="event-name"><a class="collapsed" data-toggle="collapse" href="#collapseError1"><span class="icon-play"></span> </a>Deployment Failed                        
                        <div class="event-details collapse" id="collapseError1">
                            <span class="text-error"><strong>ERROR STUFF HERE.</strong> Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.</span>                        
                        </div>
                    </div>
                    <div class="event-pile"><span class="icon-layers"></span> Dev</div>
                    <div class="event-site"><span class="icon-browser"></span> jfalotico.com</div>
                </div>
                <div class="event">
                    <div class="event-status event-status-warning"></div>
                    <div class="event-name">Deployment Started</div>
                    <div class="event-pile"><span class="icon-layers"></span> Dev</div>
                    <div class="event-site"><span class="icon-browser"></span> jfalotico.com</div>
                </div>
                <div class="event">
                    <div class="event-status event-status-success"></div>
                    <div class="event-name">Deployment Successful</div>
                    <div class="event-pile"><span class="icon-layers"></span> Dev</div>
                    <div class="event-site"><span class="icon-server"></span> jfalotico.com</div>
                </div>
                <div class="event">
                    <div class="event-status event-status-success"></div>
                    <div class="event-name">Site Installed</div>
                    <div class="event-pile"><span class="icon-layers"></span> Dev</div>
                    <div class="event-site"><span class="icon-server"></span> jfalotico.com</div>
                </div>
                <div class="event">
                    <div class="event-status event-status-error"></div>
                    <div class="event-name">Deployment Failed</div>
                    <div class="event-pile"><span class="icon-layers"></span> Dev</div>
                    <div class="event-site"><span class="icon-browser"></span> jfalotico.com</div>
                </div>
                <div class="event">
                    <div class="event-status event-status-warning"></div>
                    <div class="event-name">Deployment Started</div>
                    <div class="event-pile"><span class="icon-layers"></span> Dev</div>
                    <div class="event-site"><span class="icon-browser"></span> jfalotico.com</div>
                </div>
            </div>
        
            
            <p v-for="event in events">
                {{ event.id }} - {{ event.internal_type }} - {{ event.event_type }} - {{ event.description }} - {{
                event.data }} - {{ event.log }} - {{ event.created_at }}
            </p>
        </div>
        
    
    
        
    </footer>
</template>

<script>

    Vue.directive('watch-scroll', {
        update: function (el, bindings) {

            $(el).unbind('scroll');

            var pagination = bindings.value;

            var nextPage = pagination.current_page + 1;
            if (nextPage <= pagination.last_page) {
                $(el).bind('scroll', function () {
                    var $el = $(el);
                    if (el.scrollHeight - $el.scrollTop() - $el.outerHeight() < 1) {
                        eventStore.dispatch('getEvents', nextPage);
                    }
                });
            }
        }
    });

    export default {
        created () {
            this.fetchData();
        },
        methods: {
            fetchData: function () {
                serverStore.dispatch('getServers', function () {
                    _(serverStore.state.servers).forEach(function (server) {
                        if(server.progress != 100) {
                            Echo.private('App.Models.Server.' + server.id)
                                    .listen('Server\\ServerProvisionStatusChanged', (data) => {
                                        server.status = data.status;
                                        server.progress = data.progress;
                                        server.ip = data.ip;
                                        server.ssh_connection = data.connected;
                                    });
                        }

                    });
                });

                siteStore.dispatch('getSites', function () {
                    _(siteStore.state.sites).forEach(function (site) {
                        Echo.private('App.Models.Site.' + site.id)
                                .listen('Site\\DeploymentStepStarted', (data) => {
                                    console.info(data.step+' started');
                                })
                                .listen('Site\\DeploymentStepCompleted', (data) => {
                                    console.log(data);
                                    console.info(data.step+' completed in ' + data.deploymentEvent.runtime + ' seconds');
                                })
                                .listen('Site\\DeploymentStepFailed', (data) => {
                                    console.info(data.step+' failed');
                                })
                    });
                });

                eventStore.dispatch('getEvents');
            }
        },
        computed: {
            servers () {
                return serverStore.state.servers;
            },
            events: () => {
                return eventStore.state.events;
            },
            events_pagination: () => {
                return eventStore.state.events_pagination;
            }
        },
    }

</script>