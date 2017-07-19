export const get = ({ dispatch }) => {
    return Vue.request()
        .get(Vue.action('SiteSiteController@index'), 'user_sites/setAll')
        .then(sites => {
            _.each(sites, function(site) {
                dispatch('listen', site);
            });
        });
};

export const show = (context, site) => {
    return Vue.request().get(
        Vue.action('SiteSiteController@show', { site: site }),
        'user_sites/set',
    );
};

export const store = ({ dispatch }, data) => {
    return Vue.request(data)
        .post(Vue.action('SiteSiteController@store'), 'user_sites/add')
        .then(site => {
            dispatch('user_piles/change', data.pile_id, {
                root: true,
            }).then(() => {
                dispatch('listen', site);
                dispatch('user_piles/get', null, { root: true });
                app.$router.push({
                    name: 'site_overview',
                    params: { site_id: site.id },
                });
                return site;
            });
        });
};

export const update = (context, data) => {
    return Vue.request(data)
        .patch(Vue.action('SiteSiteController@update', { site: data.site }), [
            'user_sites/set',
            'user_sites/update',
        ])
        .then(() => {
            app.showSuccess('You have updated the site');
        });
};

export const destroy = ({ dispatch }, site) => {
    return Vue.request(site)
        .delete(Vue.action('SiteSiteController@destroy', { site: site }), [
            'user_sites/remove',
        ])
        .then(() => {
            dispatch('user_piles/get', null, { root: true });
            app.$router.push('/');
            app.showSuccess('You have deleted the site');
        });
};

export const listen = ({ commit, state, dispatch }, site) => {
    if (_.indexOf(state.listening_to, site.id) === -1) {
        commit('listenTo', site.id);
        Echo.private('App.Models.Site.Site.' + site.id)
            .listen('Site\\DeploymentStepStarted', data => {
                commit('events/updateDeployment', data, { root: true });
                commit(
                    'user_sites/updateLastDeploymentStatus',
                    {
                        site: data.site_deployment.site_id,
                        status: data.site_deployment.status,
                    },
                    {
                        root: true,
                    },
                );
            })
            .listen('Site\\DeploymentStepCompleted', data => {
                commit('events/updateDeployment', data, { root: true });
                commit(
                    'user_sites/updateLastDeploymentStatus',
                    {
                        site: data.site_deployment.site_id,
                        status: data.site_deployment.status,
                    },
                    {
                        root: true,
                    },
                );
            })
            .listen('Site\\DeploymentStepFailed', data => {
                commit('events/updateDeployment', data, { root: true });
                commit(
                    'user_sites/updateLastDeploymentStatus',
                    {
                        site: data.site_deployment.site_id,
                        status: data.site_deployment.status,
                    },
                    {
                        root: true,
                    },
                );
            })
            .listen('Site\\DeploymentCompleted', data => {
                commit('events/updateDeployment', data, { root: true });
                commit(
                    'user_sites/updateLastDeploymentStatus',
                    {
                        site: data.site_deployment.site_id,
                        status: data.site_deployment.status,
                    },
                    {
                        root: true,
                    },
                );
            })
            .notification(notification => {
                if (
                    notification.type ===
                    'App\\Notifications\\Site\\NewSiteDeployment'
                ) {
                    dispatch(
                        'user_site_deployments/getDeployment',
                        {
                            site: notification.siteDeployment.site_id,
                            deployment: notification.siteDeployment.id,
                        },
                        {
                            root: true,
                        },
                    );
                }
            });
    }
};

export const updateWorkflow = (context, data) => {
    return Vue.request(
        data,
    ).post(
        Vue.action('SiteSiteWorkflowController@store', { site: data.site }),
        ['user_sites/set', 'user_sites/update'],
    );
};
