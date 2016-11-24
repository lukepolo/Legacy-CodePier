<template>
    <div class="event">
        <div class="event-status" :class="{'event-status-neutral' : !event.status, 'event-status-success' : event.status == 'Completed', 'event-status-error' : event.status == 'Failed', 'icon-spinner' : event.status == 'Running'}"></div>
        <div class="event-name">
            <a class="collapsed" data-toggle="collapse" :href="'#' + event.id">
                <span class="icon-play"></span>
            </a> Deployment

            <div class="event-details collapse" :id="event.id">

                <template v-for="server_deployment in event.server_deployments">

                    <div class="event-status" :class="{'event-status-neutral' : (! server_deployment.failed && ! server_deployment.completed && !server_deployment.started), 'event-status-success' : server_deployment.completed, 'event-status-error' : server_deployment.failed, 'icon-spinner' : (!server_deployment.failed && !server_deployment.completed && server_deployment.started) }"></div>

                    <a class="collapsed" data-toggle="collapse" :href="'#' + event.id + '_server_deployment_' + server_deployment.server.id">
                        <span class="icon-play"></span>
                    </a>

                    {{ server_deployment.server.name }} ({{ server_deployment.server.ip }}) - {{ server_deployment.status }}

                    <div class="event-details collapse" :id="event.id + '_server_deployment_' + server_deployment.server.id">

                        <ul>
                            <template v-for="deployment_event in server_deployment.events">
                                <li :class="{'event-error' : deployment_event.failed }">

                                    <template v-if="!deployment_event.started || (deployment_event.started && deployment_event.completed) || (deployment_event.started && deployment_event.failed)">
                                        <div class="event-status" :class="{'event-status-neutral' : (! deployment_event.failed && ! deployment_event.completed), 'event-status-success' : deployment_event.completed, 'event-status-error' : deployment_event.failed}"></div>
                                    </template>
                                    <template v-else>
                                        <span class="icon-spinner"></span>
                                    </template>

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
        <div class="event-commit"><a target="_blank" :href="'https://'+ event.site.user_repository_provider.repository_provider.url + '/' + event.site.repository + '/commit/' + event.git_commit"><span class="icon-github"></span> </a></div>
    </div>
</template>

<script>
    export default {
        props : ['event'],
        methods: {
            filterArray(data) {
                if (Array.isArray(data)) {
                    return data.filter(String);
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