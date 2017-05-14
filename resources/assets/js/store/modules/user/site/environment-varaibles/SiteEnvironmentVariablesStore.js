export default {
    state: {
        site_environment_variables: []
    },
    actions: {
        getSiteEnvironmentVariables: ({ commit }, siteId) => {
            Vue.http.get(Vue.action('Site\SiteEnvironmentVariablesController@index', { site: siteId })).then((response) => {
                commit('SET_SITE_ENVIRONMENT_VARIABLES', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        createSiteEnvironmentVariable: ({ commit }, data) => {
            return Vue.http.post(Vue.action('Site\SiteEnvironmentVariablesController@store', { site: data.site }), data).then((response) => {
                commit('ADD_SITE_ENVIRONMENT_VARIABLE', response.data)
                return response.data
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        deleteSiteEnvironmentVariable: ({ commit }, data) => {
            Vue.http.delete(Vue.action('Site\SiteEnvironmentVariablesController@destroy', {
                site: data.site,
                environment_variable: data.environment_variable
            })).then(() => {
                commit('REMOVE_SITE_ENVIRONMENT_VARIABLE', data.environment_variable)
            }, (errors) => {
                app.handleApiError(errors)
            })
        }
    },
    mutations: {
        ADD_SITE_ENVIRONMENT_VARIABLE: (state, siteEnvironmentVariable) => {
            state.site_environment_variables.push(siteEnvironmentVariable)
        },
        REMOVE_SITE_ENVIRONMENT_VARIABLE: (state, siteEnvironmentVariableId) => {
            Vue.set(state, 'site_environment_variables', _.reject(state.site_environment_variables, { id: siteEnvironmentVariableId }))
        },
        SET_SITE_ENVIRONMENT_VARIABLES: (state, siteEnvironmentVariables) => {
            state.site_environment_variables = siteEnvironmentVariables
        }
    }
}
