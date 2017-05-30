<template>
    <div class="events--item">
        <div
            class="events--item-status"
            :class="{
                'events--item-status-neutral' : event.status == 'Queued',
                'events--item-status-success' : event.status == 'Completed',
                'events--item-status-error' : event.status == 'Failed',
                'icon-spinner' : event.status == 'Running'
             }"></div>
        <div class="events--item-name">
            <drop-down-event
                    title="Deployment"
                    :event="event"
                    :type="event.event_type"
                    :prefix="event.id"
            >
                <template v-for="server_deployment in event.server_deployments">
                    <drop-down-event
                            :title="getServer(server_deployment.server_id, 'name') + ' (' + getServer(server_deployment.server_id, 'ip') + ')' + ' - ' + server_deployment.status"
                            :event="server_deployment"
                            :type="event.event_type"
                            :prefix="'server_deployment_'+server_deployment.id"
                    >
                        <ul>
                            <template v-for="deployment_event in server_deployment.events">
                                <li :class="{'events--item-error' : deployment_event.failed }" v-if="deployment_event.step">
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
        <div class="events--item-pile"><span class="icon-layers"></span> {{ getPile(getSite(event.site_id, 'pile_id'), 'name') }}</div>
        <div class="events--item-site">
            <span class="icon-browser"></span>
            {{ getSite(event.site_id, 'name') }}
        </div>

        <div class="events--item-commit">
            <a target="_blank" :href="getRepositoryUrl(event)"><span class="icon-github"></span> </a>

            <confirm dispatch="user_site_deployments/rollback" confirm_class="btn btn-small" :params="{ siteDeployment : event.id, site : event.site_id } ">
                Rollback
            </confirm>
        </div>
        <div class="events--item-time">{{ timeAgo(event.created_at) }}</div>
    </div>
</template>

<script>
    import DropDownEvent from './DropDownEvent.vue';
    export default {
        components: {
            DropDownEvent,
        },
        props: ['event'],
        methods: {
            getRepositoryUrl(event) {
                let site = this.getSite(event.site_id);
                let repositoryProvider = this.getRepositoryProvider(site.user_repository_provider_id);
                return 'https://'+ repositoryProvider.url + '/' + site.repository + '/'+repositoryProvider.commit_url+'/' + event.git_commit;
            },
            filterArray(data) {

                if (!Array.isArray(data)) {
                    data = [data]
                }

                return _.reject(
                    _.reject(data, _.isEmpty),
                    _.isNull
                )
            },
            formatSeconds(number) {
                let seconds = parseFloat(number).toFixed(2);

                if (!isNaN(seconds)) {
                    return seconds;
                }
            }
        }
    }
</script>