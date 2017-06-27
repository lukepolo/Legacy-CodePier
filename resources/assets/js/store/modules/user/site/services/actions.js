export const restartServers = (context, site) => {
    Vue.request()
        .post(Vue.action("SiteSiteController@restartServer", { site: site }))
        .then(() => {
            app.showSuccess("You have restarted your sites servers.");
        });
};
export const restartWebServices = (context, site) => {
    Vue.request()
        .post(
            Vue.action("SiteSiteController@restartWebServices", { site: site })
        )
        .then(() => {
            app.showSuccess("You have restarted your web services");
        });
};

export const restartDatabases = (context, site) => {
    Vue.request()
        .post(Vue.action("SiteSiteController@restartDatabases", { site: site }))
        .then(() => {
            app.showSuccess("You have restarted your databases");
        });
};

export const restartWorkers = (context, site) => {
    Vue.request()
        .post(
            Vue.action("SiteSiteController@restartWorkerServices", {
                site: site
            })
        )
        .then(() => {
            app.showSuccess("You have restarted your workers");
        });
};
