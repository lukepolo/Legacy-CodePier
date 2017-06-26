export const get = (context, site) => {
  return Vue.request().get(
    Vue.action('SiteSiteDeploymentsController@index', { site: site }),
    'user_site_deployments/setAll',
  );
};

export const getRunningDeployments = () => {
  return Vue.request().get(
    Vue.action('UserUserController@getRunningDeployments'),
    'user_site_deployments/setRunningDeployments',
  );
};

export const getDeploymentSteps = (context, site) => {
  return Vue.request().get(
    Vue.action('SiteSiteDeploymentStepsController@getDeploymentSteps', {
      site: site,
    }),
    'user_site_deployments/setDeploymentSteps',
  );
};

export const getSiteDeploymentSteps = (context, site) => {
  return Vue.request().get(
    Vue.action('SiteSiteDeploymentStepsController@index', { site: site }),
    'user_site_deployments/setSiteDeploymentSteps',
  );
};

export const updateSiteDeployment = (context, data) => {
  return Vue.request(data)
    .post(
      Vue.action('SiteSiteDeploymentStepsController@store', {
        site: data.site,
      }),
    )
    .then(() => {
      app.showSuccess('You have updated the site deployment');
    });
};

export const refreshDeployKey = (context, site) => {
  return Vue.request()
    .post(
      Vue.action('SiteSiteController@refreshDeployKey', { site: site }),
      'user_sites/set',
    )
    .then(() => {
      app.showSuccess('You refreshed your sites deploy key.');
    });
};

export const createDeployHook = (context, site) => {
  return Vue.request()
    .post(
      Vue.action('SiteRepositoryRepositoryHookController@store', {
        site: site,
      }),
      'user_sites/set',
    )
    .then(() => {
      app.showSuccess('You have added automatic deployments');
    });
};

export const removeDeployHook = (context, data) => {
  return Vue.request(data)
    .delete(
      Vue.action('SiteRepositoryRepositoryHookController@destroy', {
        site: data.site,
        hook: data.hook,
      }),
      'user_sites/set',
    )
    .then(() => {
      app.showSuccess('You have removed automatic deployments');
    });
};

export const deploy = ({ commit }, site) => {
  return Vue.request()
    .post(Vue.action('SiteSiteController@deploy', { site: site }))
    .then(() => {
      commit(
        'user_sites/updateLastDeploymentStatus',
        {
          site: site,
          status: 'Queued',
        },
        {
          root: true,
        },
      );

      app.showSuccess('Your site deployment has been queued.');
    });
};

export const rollback = (context, data) => {
  return Vue.request(data)
    .post(Vue.action('SiteSiteController@rollback', { site: data.site }))
    .then(() => {
      app.showSuccess('You are rolling back a deployment.');
    });
};

export const getDeployment = ({ commit }, data) => {
  return Vue.request(data).get(
    Vue.action('SiteSiteDeploymentsController@show', {
      site: data.site,
      deployment: data.deployment,
    }),
    'events/add',
  );
};

export const updateSiteDeploymentConfig = (context, data) => {
  return Vue.request(data)
    .post(
      Vue.action('SiteSiteDeploymentsController@store', { site: data.site }),
      'user_sites/set',
      'user_sites/update',
    )
    .then(() => {
      app.showSuccess('You have updated the site deployment config');
    });
};
