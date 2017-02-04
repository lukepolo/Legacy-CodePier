export default {
    state: {
        site_schemas: []
    },
    actions: {
        getSiteSchemas: ({ commit }, site) => {
            Vue.http.get(Vue.action('Site\SiteSchemaController@index', { site: site })).then((response) => {
                commit('SET_SITE_SCHEMAS', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        createSiteSchema: ({ commit }, data) => {
            Vue.http.post(Vue.action('Site\SiteSchemaController@store', { site: data.site }), data).then((response) => {
                commit('ADD_SITE_SCHEMA', response.data)
            }, (errors) => {
                app.handleApiError(errors)
            })
        },
        deleteSiteSchema: ({ commit }, data) => {
            Vue.http.delete(Vue.action('Site\SiteSchemaController@destroy', {
                site: data.site,
                schema: data.schema
            })).then(() => {
                commit('REMOVE_SITE_SCHEMA', data.schema)
            }, (errors) => {
                app.handleApiError(errors)
            })
        }
    },
    mutations: {
        ADD_SITE_SCHEMA: (state, schema) => {
            state.site_schemas.push(schema)
        },
        REMOVE_SITE_SCHEMA: (state, schema) => {
            Vue.set(state, 'site_schemas', _.reject(state.site_schemas, { id: schema }))
        },
        SET_SITE_SCHEMAS: (state, siteSchemas) => {
            state.site_schemas = siteSchemas
        }
    }
}
