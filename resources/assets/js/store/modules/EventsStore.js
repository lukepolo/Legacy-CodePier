export default {
    state: {
        events: [],
        events_pagination: null,
        version : Laravel.version,
    },
    actions: {
        getEvents: ({commit, state}, data) => {

            let filters = (data && data.filters) ? data.filters : null;

            filters =_.merge({
                    page: data ? data.page : 1
                },
                _.omitBy({
                    types : filters ? _.omitBy(filters.types, _.isEmpty) : null,
                    piles : filters ? filters.piles : null,
                    sites : filters ? filters.sites : null,
                    servers : filters ? filters.servers : null
                }, _.isEmpty)
            );

            Vue.http.post(Vue.action('EventController@store'), filters).then((response) => {
                commit('SET_EVENTS', response.data);
            }, (errors) => {
                app.handleApiError(errors);
            });
        },
        setVersion : ({commit}, data) => {
            commit('SET_VERSION', data.version);
        }
    },
    mutations: {
        CLEAR_EVENTS : (state) =>{
            state.events = [];
        },
        SET_EVENTS: (state, events_pagination) => {

            _.forEach(events_pagination.data, function (event) {
                state.events.push(event);
            });

            state.events_pagination = events_pagination;
        },
        ADD_NEW_SITE_DEPLOYMENT: (state, deployment) => {
            state.events.unshift(deployment);
        },

        // TODO - we need to add the type
        UPDATE_DEPLOYMENT_EVENT : (state, event) => {

            let site_deployment = _.find(state.events, {id : event.site_deployment.id});
            let server_deployment = _.find(site_deployment.server_deployments, {id : event.server_deployment.id });

            if(server_deployment) {
                Vue.set(
                    server_deployment.events,
                    _.findKey(server_deployment.events, { id : event.deployment_event.id }),
                    event.deployment_event
                );
            }

        },
        UPDATE_SITE_DEPLOYMENT_EVENT : (state, event) => {

            let siteDeploymentKey = _.findKey(state.events, {id : event.site_deployment.id});
            let siteDeployment = state.events[siteDeploymentKey];

            _.each(event.site_deployment, function(value, key) {
                if(key != 'server_deployments') {
                    siteDeployment[key] = value;
                }
            });
        },
        UPDATE_SERVER_DEPLOYMENT_EVENT : (state, event) => {

            let site_deployment = _.find(state.events, {id: event.site_deployment.id});
            let server_deployment = _.find(site_deployment.server_deployments, {id: event.server_deployment.id});

            _.each(event.server_deployment, function (value, key) {
                server_deployment[key] = value;
            });
        },
        SET_VERSION : (state, version) => {
            state.version = version;
        },
        UPDATE_EVENT_COMMAND : (state, command) => {
            let commandKey = _.findKey(state.events, {
                id : command.id,
                event_type : command.event_type
            });

            if(!commandKey) {
                state.events.unshift(command);
                return;
            }

            Vue.set(state.events, commandKey, command);
        }
    }
}