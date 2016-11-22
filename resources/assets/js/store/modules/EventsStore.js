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
                app.showError(errors);
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
        UPDATE_DEPLOYMENT_EVENT : (state, event) => {

            var site_deployment = _.find(state.events, {id : event.site_deployment.id});
            var server_deployment = _.find(site_deployment.server_deployments, {id : event.server_deployment.id });

            Vue.set(
                server_deployment.events,
                _.findKey(server_deployment.events, { id : event.deployment_event.id }),
                event.deployment_event
            );
        },
        UPDATE_SITE_DEPLOYMENT_EVENT : (state, event) => {

            var siteDeploymentKey = _.findKey(state.events, {id : event.site_deployment.id});
            var siteDeployment = state.events[siteDeploymentKey];

            _.each(event.site_deployment, function(value, key) {
                if(key != 'server_deployments') {
                    siteDeployment[key] = value;
                }
            });
        },
        UPDATE_SERVER_DEPLOYMENT_EVENT : (state, event) => {

            var site_deployment = _.find(state.events, {id : event.site_deployment.id});
            var server_deployment = _.find(site_deployment.server_deployments, {id : event.server_deployment.id });

            _.each(event.server_deployment, function(value, key) {
                server_deployment[key] = value;
            });
        }
    }
}