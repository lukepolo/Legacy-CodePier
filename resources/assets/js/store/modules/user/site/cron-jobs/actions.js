export const get = (context, site) => {
  return Vue.request().get(
    Vue.action("SiteSiteCronJobController@index", { site: site }),
    "user_site_cron_jobs/setAll"
  );
};

export const store = (context, data) => {
  return Vue.request(data).post(
    Vue.action("SiteSiteCronJobController@store", { site: data.site }),
    "user_site_cron_jobs/add"
  );
};

export const patch = (context, data) => {
  return Vue.request(data).put(
    Vue.action("SiteSiteCronJobController@update", {
      site: data.site,
      cron_job: data.cron_job
    }),
    "user_site_cron_jobs/update"
  ).then(() => {
    app.showSuccess('Updated Cron Job');
  });
};

export const destroy = (context, data) => {
  return Vue.request(data).delete(
    Vue.action("SiteSiteCronJobController@destroy", {
      site: data.site,
      cron_job: data.cron_job
    }),
    "user_site_cron_jobs/remove"
  );
};
