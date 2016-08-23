<style>
    footer {
        max-height: 200px;
        overflow-y: scroll;
    }
</style>
<template>
    <footer v-watch-scroll="events_pagination">
        some kind of filter bar / Deployments / Regular Events
        <p v-for="event in events">
            {{ event.id }} - {{ event.internal_type }} - {{ event.event_type }} - {{ event.description }} - {{
            event.data }} - {{ event.log }} - {{ event.created_at }}
        </p>
        never ending scroll here
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
                            Echo.private('Server.' + server.id)
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
                        Echo.private('Site.' + site.id)
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