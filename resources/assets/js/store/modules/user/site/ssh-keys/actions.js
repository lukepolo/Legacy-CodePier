export const get = (context, site) => {
    return Vue.request().get(
        Vue.action("SiteSiteSshKeyController@index", { site: site }),
        "user_site_ssh_keys/setAll"
    );
};

export const store = (context, data) => {
    return Vue.request(data).post(
        Vue.action("SiteSiteSshKeyController@store", { site: data.site }),
        "user_site_ssh_keys/add"
    );
};

export const destroy = (context, data) => {
    return Vue.request(data).delete(
        Vue.action("SiteSiteSshKeyController@destroy", {
            site: data.site,
            ssh_key: data.ssh_key
        }),
        "user_site_ssh_keys/remove"
    );
};

export const refreshPublicKey = (context, site) => {
    return Vue.request()
        .post(
            Vue.action("SiteSiteController@refreshPublicKey", { site: site }),
            "user_sites/set"
        )
        .then(() => {
            app.showSuccess("You refreshed your sites ssh keys.");
        });
};
