<template>
    <div class="event">
        <div class="event-status" :class="{'event-status-neutral' : !event.status, 'event-status-success' : event.status == 'Completed', 'event-status-error' : event.status == 'Failed', 'icon-spinner' : event.status == 'Running'}"></div>
        <div class="event-name">
            <drop-down-event
                    title="Deployment"
                    :event="event"
                    :type="event.event_type"
                    :prefix="event.id"
            >
                <template v-for="server_deployment in event.server_deployments">
                    <drop-down-event
                            :title="server_deployment.server.name + ' (' + server_deployment.server.ip + ')' + ' - ' + server_deployment.status"
                            :event="server_deployment"
                            :type="event.event_type"
                            :prefix="'server_deployment_'+server_deployment.id"
                    >
                        <ul>
                            <template v-for="deployment_event in server_deployment.events">
                                <li :class="{'event-error' : deployment_event.failed }">
                                    <drop-down-event
                                            :title="deployment_event.step.step + (deployment_event.completed ? ' took ' + formatSeconds(deployment_event.runtime) + ' seconds' : '')"
                                            :event="deployment_event"
                                            :type="event.event_type"
                                            :prefix="'server_deployment_event_' + deployment_event.id"
                                            :dropdown="filterArray(deployment_event.log).length > 0"
                                    >
                                    <pre v-for="log in filterArray(deployment_event.log)">{{ log }}</pre>
                                    </drop-down-event>
                                </li>
                            </template>
                        </ul>
                    </drop-down-event>
                </template>
            </drop-down-event>
        </div>
        <div class="event-pile"><span class="icon-layers"></span> {{ event.site.pile.name }}</div>
        <div class="event-site"><span class="icon-browser"></span> {{ event.site.name }}</div>
        <div class="event-commit"><a target="_blank" :href="'https://'+ event.site.user_repository_provider.repository_provider.url + '/' + event.site.repository + '/commit/' + event.git_commit"><span class="icon-github"></span> </a></div>
    </div>
</template>

<script>
    import DropDownEvent from './DropDownEvent.vue';
    export default {
        components : {
            DropDownEvent,
        },
        props : ['event'],
        methods: {
            filterArray(data) {
                if (Array.isArray(data)) {
                    return _.reject(
                        _.reject(data, _.isEmpty),
                        _.isNull
                    )
                }

                return [];
            },
            formatSeconds(number) {
                var seconds = parseFloat(number).toFixed(2);

                if(!isNaN(seconds)) {
                    return seconds;
                }
            }
        }
    }
</script>