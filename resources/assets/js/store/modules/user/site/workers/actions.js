export const get = (context, site) => {
    return Vue.request().get(
        Vue.action("SiteSiteWorkerController@index", { site: site }),
        "user_site_workers/setAll"
    );
};

export const store = (context, data) => {
    return Vue.request(data).post(
        Vue.action("SiteSiteWorkerController@store", { site: data.site }),
        "user_site_workers/add"
    );
};

export const destroy = (context, data) => {
    return Vue.request(data).delete(
        Vue.action("SiteSiteWorkerController@destroy", {
            site: data.site,
            worker: data.worker
        }),
        "user_site_workers/remove"
    );
};
