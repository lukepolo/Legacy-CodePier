export const get = (context, site) => {
    return Vue.request().get(
        Vue.action("SiteSiteSchemaController@index", { site: site }),
        "user_site_schemas/setAll"
    );
};

export const store = (context, data) => {
    return Vue.request(data).post(
        Vue.action("SiteSiteSchemaController@store", { site: data.site }),
        "user_site_schemas/add"
    );
};

export const destroy = (context, data) => {
    return Vue.request(data).delete(
        Vue.action("SiteSiteSchemaController@destroy", {
            site: data.site,
            schema: data.schema
        }),
        "user_site_schemas/remove"
    );
};
