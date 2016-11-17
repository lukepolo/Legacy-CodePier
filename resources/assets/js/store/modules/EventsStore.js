export default {
    state: {
        events: [],
        events_pagination: null
    },
    actions: {
        getEvents: ({commit}, page) => {
            Vue.http.get(Vue.action('EventController@index', {page: page ? page : 1})).then((response) => {
                commit('SET_EVENTS', response.data);
            }, (errors) => {
                alert(errors);
            });
        }
    },
    mutations: {
        SET_EVENTS: (state, events_pagination) => {

            _.forEach(events_pagination.data, function (event) {
                state.events.push(event);
            });

            state.events_pagination = events_pagination;
        },
        ADD_NEW_SITE_DEPLOYMENT: (state, deployment) => {
            state.events.unshift(deployment);
        },
        UPDATE_DEPLOYMENT_EVENT : (state, deploymentEvent) => {

            var site_deployment = _.find(state.events, {id : deploymentEvent.site_deployment_id});
            var server_deployment = _.find(site_deployment.server_deployments, {id : deploymentEvent.deployment_event.site_server_deployment_id });

            Vue.set(
                server_deployment.events,
                _.findKey(server_deployment.events, { id : deploymentEvent.deployment_event.id }),
                deploymentEvent.deployment_event
            );
        },
        UPDATE_SERVER_DEPLOYMENT_EVENT :(state, serverDeploymentEvent) => {

        },
        UPDATE_SITE_DEPLOYMENT_EVENT : (state, siteDeploymentEvent) => {

        }
    }
}